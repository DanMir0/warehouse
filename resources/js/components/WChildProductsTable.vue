<script setup>
import {computed, onMounted, ref} from "vue";
import {useRoute} from "vue-router";

const route = useRoute();

const props = defineProps({selectedProducts: Array, defaultSelectedProducts: Array});
const emit = defineEmits(['add-product', 'updatedProduct', 'deleteProduct']);

const products = ref([]);
const dialog = ref(false);
const defaultProduct = ref();
const headers = ref([
    {title: "Наименование", value: "product_name", sortable: true},
    {title: "Количество", value: "quantity", sortable: true},
    {title: "Единица измерения", value: "measuring_unit_name", sortable: true},
    {title: "Действия", value: "actions"},
]);

const editedItem = ref({
    id: null,
    product_name: null,
    quantity: 0,
})

function editItem(item) {
    dialog.value = true
    editedItem.value = {id: item.product_id, ...item}
    defaultProduct.value = {...editedItem.value}
}

const errors = ref({});

const dialogTitle = computed(() => route.params.id ? "Редактировать" : "Добавить");

function save() {
    errors.value = {}
    if (editedItem.value.quantity <= 0 || editedItem.value.quantity === "") {
        errors.value.quantity = "Число должно быть больше 0";
    }

    // Проверка поля product_name
    if (!editedItem.value.product_name) {
        errors.value.product = "Поле обязательное";
    }

    // Если есть ошибки, не продолжаем выполнение
    if (Object.keys(errors.value).length > 0) {
        return;
    }

    editedItem.value.quantity = parseFloat(editedItem.value.quantity);
    const product = products.value.find(p => p.name === editedItem.value.product_name)
    const newProduct = {
        measuring_unit_name: product?.measuring_unit_name,
        quantity: editedItem.value.quantity,
        product_id: product?.id,
        product_name: editedItem.value.product_name
    }

    if (editedItem.value.id !== null) {
        console.log('xui')
        emit("updatedProduct", {id: editedItem.value.id, product: newProduct}, defaultProduct.value)
    } else {
        const duplicateProduct = props.defaultSelectedProducts.some(
            (product) => product.product_name === editedItem.value.product_name
        );

        if (duplicateProduct) {
            errors.value.product = "Этот материал уже добавлен.";
            return;
        }

        emit("add-product", newProduct)
    }

    clearForm();
}

function clearForm() {
    editedItem.value = {id: null, product_name: null, quantity: 0};
    dialog.value = false;
}

function deleteItem(item) {
    emit("deleteProduct", item)
}

onMounted(() => {
    axios.get("/api/products")
        .then(response => {
            products.value = response.data;
        })
        .catch(error => {
            console.error(error);
        })
})
</script>

<template>
    <v-data-table
        :headers="headers"
        :items="selectedProducts"
        :items-per-page-text="'Строк на странице:'"
        class="elevation-1"
        :items-per-page-options="[
              {value: 10, title: '10'},
              {value: 25, title: '25'},
              {value: 50, title: '50'},
              {value: 100, title: '100'},
              {value: -1, title: 'Все'}
            ]"
        :sort-by="[{key: 'name', order: 'asc'}]">
        <template v-slot:top>
            <v-toolbar flat>
                <v-toolbar-title>Материалы</v-toolbar-title>
                <v-dialog
                    v-model="dialog"
                    max-width="600">
                    <template v-slot:activator="{ props: activatorProps }">
                        <v-btn
                            variant="tonal"
                            prepend-icon="mdi-plus"
                            text="Добавить из справочника"
                            v-bind="activatorProps"
                        ></v-btn>
                    </template>

                    <v-card :title="dialogTitle">
                        <v-card-text>
                            <v-form ref="formRef" v-slot="{ validate }">
                                <v-container>
                                    <v-row>
                                        <v-col cols="12" sm="6" md="8">
                                            <v-select
                                                v-model="editedItem.product_name"
                                                label="Товар"
                                                :items="products"
                                                item-title="name"
                                                item-id="id"
                                                :error-messages="errors.product"
                                                :rules="[v => !!v || 'Товар обязателен']">
                                            </v-select>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="4">
                                            <v-text-field
                                                v-model="editedItem.quantity"
                                                label="Количество"
                                                :error-messages="errors.quantity"
                                                :rules="[v => v > 0 || 'Количество должно быть больше 0']">
                                            </v-text-field>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </v-form>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>

                            <v-btn
                                color="blue darken-1"
                                text="Отмена"
                                variant="text"
                                @click="dialog = false"
                            ></v-btn>

                            <v-btn
                                color="blue darken-1"
                                text="Сохранить"
                                variant="text"
                                @click="save"
                            ></v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-toolbar>
        </template>
        <template v-slot:item.actions="{ item }">
            <v-icon
                class="me-2"
                size="small"
                @click="editItem(item)">
                mdi-pencil
            </v-icon>
            <v-icon
                size="small"
                @click="deleteItem(item)">
                mdi-delete
            </v-icon>
        </template>
    </v-data-table>
</template>

<style scoped>

</style>

