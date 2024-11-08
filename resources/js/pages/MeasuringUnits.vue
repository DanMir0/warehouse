<script setup>
import WTable from "../components/WTable.vue";
import {onMounted, ref} from "vue";
import router from "../router/index.js";

const measuringUnits = ref([]);

const alertMessage = ref("");
const alertType = ref("");

const dialogDelete = ref(false)

const headers = ref([
    {title: "Код", value: "id", sortable: true},
    {title: "Наименование", value: "name", sortable: true},
    {title: "Действия", value: "actions"},
])

const editedItem = ref({
    id: 0,
    name: "",
})

onMounted(() => {
    axios.get("/api/measuring_units")
        .then(response => {
            measuringUnits.value = response.data
        })
})

function addItem() {
    router.push("/measuring_units/create")
}

function editItem(item) {
    router.push(`/measuring_units/${item.id}/edit`)
}

function deleteItemConfirm(id) {
    axios.delete(`/measuring_units/${id}`)
        .then(response => {
            dialogDelete.value = false;
            measuringUnits.value = measuringUnits.value.filter(measuringUnit => measuringUnit.id !== id);
            alertMessage.value = "Единица измерения удалена.";
            alertType.value = "success";
        })
        .catch(error => {
            alertMessage.value = error.messages;
            alertType.value = "error";
        });
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
        title="Единицы измерения"
        btn-icon="mdi-beaker-plus-outline"
        :headers="headers"
        :items="measuringUnits"
        :edited-item="editedItem"
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
