<script setup>
import {onMounted, ref} from "vue";
import router from "../router/index.js";
import {setAlert} from "../helpers/helpers.js";
import useFormHandler from "../composoble/useFormHandler.js";
import {deleteDocumentTypes, fetchDocumentTypes} from "../services/documentTypeService.js";

const {alertMessage, alertType, handlerResponse} = useFormHandler()

let documentTypes = ref([]);

const dialogDelete = ref(false);

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

async function deleteItemConfirm(id) {
    const {success, message} = await handlerResponse(deleteDocumentTypes(id));
    setAlert(alertMessage, alertType, success ? "Тип документа удален" : message, success ? "success" : "error")
    if (success) {
        dialogDelete.value = false;
        documentTypes.value = documentTypes.value.filter(documentType => documentType.id !== id);
    }
}

onMounted(async () => {
    const {success, message, data} = await handlerResponse(fetchDocumentTypes());
    setAlert(alertMessage, alertType, message, "error")
    if (success) {
        documentTypes.value = data;
    }
})
</script>

<template>
    <v-alert
        v-show="alertMessage"
        class="alert"
        :title="alertMessage"
        :type="alertType || 'info'"
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
    position: fixed;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    border-radius: 8px;
}
</style>
