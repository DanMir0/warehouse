<script setup>
import {onMounted, ref} from "vue";

const emit = defineEmits(["save-product"])

const entity = ref({
    products: null,
    quantity: 0,
});

const addDialog = ref(false);

const products = ref([]);

function addItem() {
    addDialog.value = true
}

function close() {
    addDialog.value = false
}

onMounted(() => {
    axios.get("/api/products")
        .then(response => {
            products.value = response.data
        })
        .catch(error => {
            console.error(error);
        })
})
</script>

<template>
    <v-data-table
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
            <v-toolbar
                flat>
                <v-toolbar-title>Материалы</v-toolbar-title>
                <v-btn
                    color="secondary"
                    dark
                    @click="addItem">
                    Добавить из справочника
                </v-btn>
                <v-dialog v-model="addDialog" max-width="500px">
                    <v-card>
                        <v-card-title class="text-h5">{{ formTitle }}</v-card-title>

                        <v-card-text>
                            <v-form>
                                <v-container>
                                    <v-row>
                                        <v-col cols="12" sm="6" md="8">
                                            <v-select
                                                v-model="entity.products"
                                                label="Товар"
                                                item-title="name"
                                                item-value="id"
                                                :items="products"
                                                :rules="rules"
                                                >
<!--                                                :error-messages="errors.products"-->
                                            </v-select>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="4">
                                            <v-text-field
                                                v-model="entity.quantity"
                                                label="Количество"
                                                :rules="rules">
                                            </v-text-field>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </v-form>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="blue-darken-1" variant="text" @click="close">Отмена</v-btn>
                            <v-btn color="blue-darken-1" variant="text"
                                   @click="emit('save-product', entity)">Сохранить
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>

            </v-toolbar>
        </template>
        <template v-slot:item.actions="{ item }">
            <v-icon
                class="me-2"
                size="small">
                mdi-pencil
            </v-icon>
            <v-icon
                size="small"
                @click="deleteItem(item)">
                mdi-delete
            </v-icon>
        </template>
        <!--        <template v-slot:no-data>-->
        <!--            <v-btn-->
        <!--                color="primary"-->
        <!--                @click="initialize"-->
        <!--            >-->
        <!--                Reset-->
        <!--            </v-btn>-->
        <!--        </template>-->
    </v-data-table>
</template>

<style scoped>

</style>

