<script setup>
import {onMounted, ref} from "vue";
import router from "../router/index.js";

const techCards = ref([]);

const dialogDelete = ref(false);
const alertMessage = ref("");
const alertType = ref("");

let headers = ref([
    {title: "Код", value: "id", sortable: true},
    {title: "Наименование", value: "name", sortable: true},
    {title: "Готовая продукция", value: "product_name", sortable: true},
    {title: "Дата создания", value: "created_at", sortable: true},
    {title: "Обновленная дата", value: "updated_at", sortable: true},
    {title: "Действия", value: "actions"},
]);

const editedItem = ref({
    id: 0,
    name: "",
    product_name: "",
    created_at: null,
    updated_at: null,
})

function addItem() {
    router.push("/tech_cards/create");
}

function editItem(item) {
    router.push(`/tech_cards/${item.id}/edit`)
}

function deleteItemConfirm(id) {
    axios.delete(`/tech_cards/${id}`)
        .then(response => {
            dialogDelete.value = false;
            techCards.value = techCards.value.filter(orderStatus => orderStatus.id !== id);
            alertMessage.value = "Тех карта удалена";
            alertType.value = "success";
        })
        .catch(error => {
            alertMessage.value = error.message;
            alertType.value = "error";
        })
}

onMounted(() => {
    axios.get("/api/tech_cards")
        .then(response => {
            techCards.value = response.data.map(item => ({
                ...item,
                created_at: new Date(item.created_at).toLocaleDateString(),
                updated_at: new Date(item.updated_at).toLocaleDateString(),
            }));
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
        :headers="headers"
        :items="techCards"
        :editedItem="editedItem"
        v-model="dialogDelete"
        title="Тех карты"
        btn-icon="mdi-cart-plus"
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
