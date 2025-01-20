<script setup>
import {computed, ref} from "vue";
import {useRoute} from "vue-router";

defineProps({selectedProducts: Array})
const emit = defineEmits(['add-product', 'updated-product', 'delete-products'])

const route = useRoute();
const dialog = ref(false);
const errors = ref({});
const headers = ref([
    {title: "Наименование", value: "product_name", sortable: true},
    {title: "Количество", value: "quantity", sortable: true},
]);
const editedItem = ref({
   id: null,
});

const dialogTitle = computed(() => route.params.id ? "Редактировать" : "Добавить");

function editItem(item) {

}

function deleteItem(item) {

}

function save() {

}
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
                <v-toolbar-title>Товары</v-toolbar-title>
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
