<script setup>
import {onMounted, ref} from "vue";
import router from "../router/index.js";
import useFormHandler from "../composoble/useFormHandler.js";
import {fetchDocuments, deleteDocument} from "../services/documenServices.js";
import {setAlert, formatDate} from "../helpers/helpers.js";

const {alertMessage, alertType, handlerResponse} = useFormHandler()

const dialogDelete = ref(false);
const documents = ref([]);

let headers = ref([
    {title: "Код", value: "id", sortable: true},
    {title: "Тип документа", value: "document_type_name", sortable: true},
    {title: "Контрагенты", value: "counterparty_name", sortable: true},
    {title: "Дата создания", value: "created_at", sortable: true},
    {title: "Действия", value: "actions"},
]);

const editedItem = ref({
    id: 0,
    document_type_name: "",
    counterparties_name: "",
    created_at: 0,
});

function addItem() {
    router.push("/documents/create");
}

function editItem(item) {
    router.push(`/documents/${item.id}/edit`);
}

async function deleteItemConfirm(id) {
    const {success, message} = await handlerResponse(deleteDocument(id));
    setAlert(alertMessage, alertType, success ? "Документ удален." : message, success ? "success" : "error");
    dialogDelete.value = false;
    if (success) {
        documents.value = documents.value.filter(techCard => techCard.id !== id);
    }

}

onMounted(async () => {
    const {success, message, data} = await handlerResponse(fetchDocuments());
    setAlert(alertMessage, alertType, message, "error")
    if (success) {
        documents.value = data.map(document => ({
            ...document,
            created_at: formatDate(document.created_at),
        }));

    }
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
        title="Документы"
        btn-icon="mdi-file-document-plus-outline"
        :headers="headers"
        :items="documents"
        :editedItem="editedItem"
        v-model="dialogDelete"
        @add-item="addItem"
        @edit-item="editItem"
        @delete-item-confirm="deleteItemConfirm">
    </w-table>
</template>

<style scoped>

</style>
