<script setup>
import {onMounted, ref} from "vue";
import router from "../router/index.js";
import {setAlert} from "../helpers/helpers.js";
import {fetchOrderStatuses, deleteOrderStatuses} from "../services/orderStatusService.js";
import useFormHandler from "../composoble/useFormHandler.js";

const {alertMessage, alertType, handlerResponse} = useFormHandler()

let orderStatuses = ref([]);
const loading = ref(true);
let headers = ref([
    {title: "Код", value: "id", sortable: true},
    {title: "Наименование", value: "name", sortable: true},
    {title: "Действия", value: "actions"},
]);

const dialogDelete = ref(false);

const editedItem = ref({
    id: 0,
    name: "",
})

function addItem() {
    router.push("order_statuses/create");
}

function editItem(item) {
    router.push(`order_statuses/${item.id}/edit`)
}

async function deleteItemConfirm(id) {
    const {success, message} = await handlerResponse(deleteOrderStatuses(id));
    setAlert(alertMessage, alertType, success ? "Статус заказа удален." : message, success ? "success" : "error");
    if (success) {
        dialogDelete.value = false;
        orderStatuses.value = orderStatuses.value.filter(orderStatus => orderStatus.id !== id);
    }
}

onMounted(async () => {
    loading.value = true;
    const {success, message, data} = await handlerResponse(fetchOrderStatuses())
    setAlert(alertMessage, alertType, message, "error");
    if (success) {
        orderStatuses.value = data
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
        title="Статусы производства"
        btn-icon="mdi-plus"
        :loading="loading"
        :headers="headers"
        :items="orderStatuses"
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
