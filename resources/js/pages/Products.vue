<script setup>
import {onMounted, ref} from "vue";
import router from "../router/index.js";

let products = ref([]);

let headers = ref([
    {title: "Код", value: "id", sortable: true},
    {title: "Наименование", value: "name", sortable: true},
    {title: "Единицы измерения", value: "measuring_unit_name", sortable: true},
    {title: "Остатки", value: "residue", sortable: true},
    {title: "Действия", value: "actions"},
]);

const editedItem = ref({
    id: 0,
    name: "",
    measuring_unit_name: "",
    residue: 0,
})

const dialogDelete = ref(false)
const alertMessage = ref('')
const alertType = ref('')

onMounted(() => {
    axios.get("/api/products")
        .then(response => {
            products.value = response.data;
        })
        .catch(e => {
            console.log(e);
        })
});


function deleteItemConfirm(id) {
    axios.delete(`/products/${id}`)
        .then(response => {
            dialogDelete.value = false;
            products.value = products.value.filter(product => product.id !== id)
            alertMessage.value = "Товар удален.";
            alertType.value = "success";
        })
        .catch(error => {
            alertMessage.value = error.messages;
            alertType.value = "error";
        });
}

function addItem() {
    router.push("/products/create");
}

function editItem(item) {
    router.push(`/products/${item.id}/edit`)
}
</script>

<template>
    <w-table
        :headers="headers"
        :items="products"
        :editedItem="editedItem"
        v-model="dialogDelete"
        title="Товары"
        btn-icon="mdi-cart-plus"
        @add-item="addItem"
        @edit-item="editItem"
        @delete-item-confirm="deleteItemConfirm">
    </w-table>
    <v-alert
        v-show="alertMessage"
        class="alert"
        :title="alertMessage"
        :type="alertType"
        closable
        max-width="500">
    </v-alert>
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
