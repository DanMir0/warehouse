<script setup>
import {onMounted, ref} from "vue";
import router from "../router/index.js";
import {formatDate} from "../helpers/helpers.js";
import {setAlert} from "../helpers/helpers.js";
import useFormHandler from "../composoble/useFormHandler.js";
import {deleteTechCard, fetchTechCards} from "../services/tehcCardServices.js";

const {alertMessage, alertType, handlerResponse} = useFormHandler()

const techCards = ref([]);

const dialogDelete = ref(false);

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

async function deleteItemConfirm(id) {
    const {success, message} = await handlerResponse(deleteTechCard(id))
    setAlert(alertMessage, alertType, success ? "Тех карта удалена." : message, success ? "success" : "error");
    if (success) {
        dialogDelete.value = false;
        techCards.value = techCards.value.filter(techCard => techCard.id !== id);
    }
}

onMounted(async () => {
    const {success, message, data} = await handlerResponse(fetchTechCards())
    setAlert(alertMessage, alertType, message, "error");
    if (success) {
        techCards.value = data.map(techCard => ({
            ...techCard,
            created_at: formatDate(techCard.created_at),
            updated_at: formatDate(techCard.updated_at),
        }))
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
        v-if="techCards.length"
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
    position: fixed;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    border-radius: 8px;
}
</style>
