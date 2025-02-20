<script setup>
import {onMounted, ref} from "vue";
import router from "../router/index.js";
import {formatDate, setAlert} from "../helpers/helpers.js";
import useFormHandler from "../composoble/useFormHandler.js";
import {deleteOrder, fetchOrders} from "../services/orderServices.js";

const {alertMessage, alertType, handlerResponse} = useFormHandler()

const dialogDelete = ref(false);
const orders = ref([]);
const headers = ref([
    {title: "Код", value: "id", sortable: true},
    {title: "Статус заказа", value: "order_status_name", sortable: true},
    {title: "Контрагент", value: "counterparty_name", sortable: true},
    {title: "Дата создания", value: "created_at", sortable: true},
    {title: "Дата обновления", value: "updated_at", sortable: true},
    {title: "Дата окончания", value: "finished_at", sortable: true},
    {title: "Действия", value: "actions"},
]);

const editedItem = ref({
    id: 0,
    order_status_name: null,
    counterparty_name: null,
    created_at: null,
    updated_at: null,
    finished_at: null,
})

function addItem() {
    router.push("/orders/create")
}

function editItem(item) {
    router.push(`/orders/${item.id}/edit`)
}

async function deleteItemConfirm(id) {
    const {success, message} = await handlerResponse(deleteOrder(id));
    setAlert(alertMessage, alertType, success ? "Заказ удален." : message, success ? "success" : "error");
    if (success) {
        dialogDelete.value = false;
        orders.value = orders.value.filter(order => order.id !== id);
    }
}

onMounted(async () => {
    const {success, message, data} = await handlerResponse(fetchOrders());
    setAlert(alertMessage, alertType, message, "error");
    if (success) {
        orders.value = data.map(order => ({
            ...order,
            created_at: formatDate(order.created_at),
            updated_at: formatDate(order.updated_at),
        }))
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
        title="Заказы"
        btn-icon="mdi-plus"
        :headers="headers"
        :items="orders"
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
