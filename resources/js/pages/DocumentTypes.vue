<script setup>
import {onMounted, ref} from "vue";
import router from "../router/index.js";

let documentTypes = ref([]);

const dialogDelete = ref(false);
let alertMessage = ref('');
let alertType = ref('');

let headers = ref([
    {title: "Код", value: "id", sortable: true},
    {title: "Наименование", value: "name", sortable: true},
    {title: "Действия", value: "actions"},
]);

const editedItem = ref({
    id: 0,
    name: "",
})

function addItem() {
    router.push('/document_types/create')
}

function editItem(item) {
    router.push(`/document_types/${item.id}/edit`)
}

function deleteItemConfirm(id) {
    axios.delete(`/document_types/${id}`)
        .then(response => {
            dialogDelete.value = false;
            documentTypes.value = documentTypes.value.filter(documentType => documentType.id !== id);
            alertMessage.value = "Тип документа удален";
            alertType.value = "success";
        })
        .catch(error => {
            alertMessage.value = error.message;
            alertType.value = "error";
        })
}

onMounted(() => {
    axios.get('/api/document_types')
        .then(response => {
            documentTypes.value = response.data;
        })
        .catch(error => {
            console.log(error)
        })
})
</script>

<template>
    <v-alert
        v-show="alertMessage"
        class="alert"
        :title="alertMessage"
        :type="alertType"
        closable
        max-width="500">
    </v-alert>
    <w-table
        title="Типы документов"
        btn-icon="mdi-file-document-plus-outline"
        :headers="headers"
        :items="documentTypes"
        :editedItem="editedItem"
        v-model="dialogDelete"
        @add-item="addItem"
        @edit-item="editItem"
        @delete-item-confirm="deleteItemConfirm">
    </w-table>
</template>

<style scoped>
.alert {
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    border-radius: 50% 20% / 10% 40%;
}
</style>
