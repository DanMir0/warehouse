<script setup>
import {onMounted, ref} from "vue";
import router from "../router/index.js";
import {setAlert} from "../helpers/helpers.js";
import useFormHandler from "../composoble/useFormHandler.js";
import {fetchProducts, deleteProduct} from "../services/productServices.js";

const {alertMessage, alertType, handlerResponse} = useFormHandler()

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

async function deleteItemConfirm(id) {
    const {success, message} = await handlerResponse(deleteProduct());
    setAlert(alertMessage, alertType, success ? "Товар удален." : message, success ? "success" : "error");
    if (success) {
        dialogDelete.value = false;
        products.value = products.value.filter(product => product.id !== id)
    }
}

function addItem() {
    router.push("/products/create");
}

function editItem(item) {
    router.push(`/products/${item.id}/edit`)
}

onMounted(async () => {
    const {success, message, data} = await handlerResponse(fetchProducts());
    setAlert(alertMessage, alertType, message, "error");
    if (success) {
        products.value = data
    }
});
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
        v-if="products.length"
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
