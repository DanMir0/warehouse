<script setup>
import {computed, ref} from "vue";
import {useRoute} from "vue-router";

defineProps({selectedDocuments: Array})

const emit = defineEmits(['updated-document']);

const route = useRoute();
const errors = ref({});
const dialog = ref(false);
const headers = ref([
    {title: "Тип документа", value: "document_type_name", sortable: true},
    {title: "Номер заказа", value: "order_id", sortable: true},
    {title: "Дата создания", value: "created_at", sortable: true},
]);
const editedItem = ref({
    id: null,

})

function editItem(item) {

}

function save() {

}
</script>

<template>
    <v-data-table
        :headers="headers"
        :items="selectedDocuments"
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
                <v-toolbar-title>Документы</v-toolbar-title>
            </v-toolbar>
        </template>
        <template v-slot:item.actions="{ item }">
            <v-icon
                class="me-2"
                size="small"
                @click="editItem(item)">
                mdi-pencil
            </v-icon>
        </template>
    </v-data-table>
</template>

<style scoped>

</style>
