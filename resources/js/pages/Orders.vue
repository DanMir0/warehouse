<script setup>
import {onMounted, ref} from "vue";
import router from "../router/index.js";
import {formatDate} from "../helpers/helpers.js";

const alertMessage = ref("");
const alertType = ref("");
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

function addItem() {
    router.push("/orders/create")
}

onMounted(() => {
    axios.get("api/orders")
        .then(response => {
            orders.value = response.data.map(item => ({
                ...item,
                created_at: formatDate(item.created_at),
                updated_at: formatDate(item.updated_at)
            }))
        })
        .catch(error => {
            console.error(error);
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
        title="Заказы"
        btn-icon="mdi-plus"
        :headers="headers"
        :items="orders"
        :edited-item="editedItem"
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
