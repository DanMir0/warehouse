<script setup>
import {onMounted, ref} from "vue";
import router from "../router/index.js";
import {setAlert} from "../helpers/helpers.js";
import useFormHandler from "../composoble/useFormHandler.js";
import {fetchMeasuringUnits, deleteMeasuringUnit} from "../services/measuringUnitService.js";

const {alertMessage, alertType, handlerResponse} = useFormHandler();

const measuringUnits = ref([]);
const loading = ref(true);
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

function addItem() {
    router.push("/measuring_units/create")
}

function editItem(item) {
    router.push(`/measuring_units/${item.id}/edit`)
}

async function deleteItemConfirm(id) {
    const {success, message} = await handlerResponse(deleteMeasuringUnit(id));
    setAlert(alertMessage, alertType, success ? "Единица измерения удалена." : message, success ? "success" : "error");
    if (success) {
        dialogDelete.value = false;
        measuringUnits.value = measuringUnits.value.filter(measuringUnit => measuringUnit.id !== id);
    }
}

onMounted(async () => {
    loading.value = true;
    const {message, success, data} = await handlerResponse(fetchMeasuringUnits());
    setAlert(alertMessage, alertType, message, "error");
    if (success) {
        measuringUnits.value = data;
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
        title="Единицы измерения"
        btn-icon="mdi-beaker-plus-outline"
        :loading="loading"
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
    position: fixed;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    border-radius: 8px;
}
</style>
