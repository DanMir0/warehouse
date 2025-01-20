<script setup>
import WTable from "../components/WTable.vue";
import {onMounted, ref} from "vue";
import router from "../router/index.js";
import {formatPhone} from "../helpers/helpers.js";

let counterparties = ref([]);

const dialogDelete = ref(false);
let alertMessage = ref('');
let alertType = ref('');

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

function deleteItemConfirm(id) {
    axios.delete(`counterparties/${id}`)
        .then(response => {
            dialogDelete.value = false;
            counterparties.value = counterparties.value.filter(counterparty => counterparty.id !== id);
            alertMessage.value = "Контрагент удален";
            alertType.value = "success";
        })
        .catch(error => {
            alertMessage.value = error.message;
            alertType.value = "error";
        })
}

onMounted(() => {
    axios.get('/api/counterparties')
        .then(response => {
            counterparties.value = response.data.map(counterparty => {
                return {
                    ...counterparty,
                    contact_info: formatPhone(counterparty.contact_info)
                }
            })
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
        title="Контрагенты"
        btn-icon="mdi-account-plus"
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
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    border-radius: 50% 20% / 10% 40%;
}
</style>
