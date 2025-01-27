<script setup>
import {onMounted, ref} from "vue";
import router from "../router/index.js";
import {setAlert} from "../helpers/helpers.js";

let orderStatuses = ref([]);

let headers = ref([
    {title: "Код", value: "id", sortable: true},
    {title: "Наименование", value: "name", sortable: true},
    {title: "Действия", value: "actions"},
]);

const dialogDelete = ref(false);
let alertMessage = ref('');
let alertType = ref('');

const editedItem = ref({
    id: 0,
    name: "",
})

onMounted(() => {
    axios.get('api/order_statuses')
        .then(response => {
            orderStatuses.value = response.data;
        })
        .catch(error => {
            console.log(error);
        })
})

function addItem() {
    router.push("order_statuses/create");
}

function editItem(item) {
    router.push(`order_statuses/${item.id}/edit`)
}

function deleteItemConfirm(id) {
    axios.delete(`/order_statuses/${id}`)
        .then(response => {
            dialogDelete.value = false;
            orderStatuses.value = orderStatuses.value.filter(orderStatus => orderStatus.id !== id);
            setAlert(alertMessage, alertType, "Статус заказа удален.", "success")
        })
        .catch(error => {
            setAlert(alertMessage, alertType, error.message, "error")
        })
}
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
        title="Статусы производства"
        btn-icon="mdi-plus"
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
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    border-radius: 50% 20% / 10% 40%;
}
</style>
