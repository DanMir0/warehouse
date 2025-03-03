<script setup>
import WTable from "../components/WTable.vue";
import {onMounted, ref} from "vue";
import router from "../router/index.js";
import {formatPhoneForDisplay, setAlert} from "../helpers/helpers.js";
import useFormHandler from "../composoble/useFormHandler.js";
import {fetchCounterparties, deleteCounterparties} from "../services/counterpartyService.js";

const {alertMessage, alertType, handlerResponse} = useFormHandler()

let counterparties = ref([]);
const loading = ref(true);
const dialogDelete = ref(false);

const headers = ref([
    {title: "Код", value: "id", sortable: true},
    {title: "Наименование", value: "name", sortable: true},
    {title: "Контактная информация", value: "contact_info", sortable: true},
    {title: "Адрес", value: "address", sortable: true},
    {title: "ИНН", value: "inn", sortable: true},
    {title: "Контактное лицо", value: "contact_persons", sortable: true},
    {title: "Действия", value: "actions"},
]);

const editedItem = ref({
    id: 0,
    name: "",
    contact_info: "",
    address: "",
    inn: 0,
    contact_persons: "",
})

function addItem() {
    router.push("/counterparties/create")
}

function editItem(item) {
    router.push(`/counterparties/${item.id}/edit`)
}

async function deleteItemConfirm(id) {
    const {success, message} = await handlerResponse(deleteCounterparties(id));
    setAlert(alertMessage, alertType, success ? "Контрагент удален." : message, success ? "success" : "error");
    if (success) {
        dialogDelete.value = false;
        counterparties.value = counterparties.value.filter(counterparty => counterparty.id !== id);
    }
}

onMounted(async () => {
    loading.value = true;
    const {success, message, data} = await handlerResponse(fetchCounterparties());
    setAlert(alertMessage, alertType, message, "error")
    if (success) {
        counterparties.value = data.map(counterparty => {
            return {
                ...counterparty,
                contact_info: formatPhoneForDisplay(counterparty.contact_info)
            }
        })
    }
    loading.value = false;
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
        title="Контрагенты"
        btn-icon="mdi-account-plus"
        :loading="loading"
        :headers="headers"
        :items="counterparties"
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
