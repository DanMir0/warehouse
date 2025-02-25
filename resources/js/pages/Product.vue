<script setup>
import {computed, onMounted, ref} from "vue";
import router from "../router/index.js";
import {useRoute} from "vue-router";
import {setAlert} from "../helpers/helpers.js";
import {requireRule} from "../helpers/validationRules.js";
import useFormHandler from "../composoble/useFormHandler.js";
import {fetchMeasuringUnits, fetchProduct, postProduct, updateProduct} from "../services/productServices.js";

defineProps({
    id: [String, Number]
})

const {alertMessage, alertType, errors, handlerResponse} = useFormHandler()

const route = useRoute();

const product = ref();

const entity = ref({
    name: null,
    measuring_unit_id: null,
});

const unitsOfMeasuring = ref([]);
const rules = [requireRule];

const formTitle = computed(() => {
    return route.params.id ? "Редактировать товар" : "Добавить товар"
})

async function save() {
    if (route.params.id) {
        const {success, message} = await handlerResponse(updateProduct(route.params.id, entity.value));
        setAlert(alertMessage, alertType, success ? "Товар добавлен." : message, success ? "success" : "error");
    } else {
        const {success, message, data} = await handlerResponse(postProduct(entity.value));
        setAlert(alertMessage, alertType, success ? "Товар добавлен." : message, success ? "success" : "error");

        await router.push(`/products/${data.id}/edit`)
    }
}

function back() {
    router.back();
}

onMounted(async () => {
    const {success, message, data} = await handlerResponse(fetchMeasuringUnits());
    setAlert(alertMessage, alertType, message, "error");
    if (success) unitsOfMeasuring.value = data;

    if (route.params.id) {
        const {success, message, data} = await handlerResponse(fetchProduct(route.params.id));
        setAlert(alertMessage, alertType, message, "error");
        if (success) entity.value = data;
    }
});
</script>

<template>
    <v-alert
        v-show="alertMessage"
        class="alert"
        :title="alertMessage"
        :type="alertType || 'info'"
        closable
        max-width="500"
        position="fixed">
    </v-alert>
    <v-card>
        <v-form>
            <v-card-title>
                <span class="text-h5">{{ formTitle }}</span>
            </v-card-title>

            <v-card-text>
                <v-container>
                    <v-row>
                        <v-col cols="12" sm="6" md="2">
                            <v-text-field
                                disabled
                                v-model="route.params.id"
                                label="Код"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="4">
                            <v-text-field
                                v-model="entity.name"
                                label="Наименование"
                                :rules="rules"
                                :error-messages="errors.name">
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="4">
                            <v-select
                                v-model="entity.measuring_unit_id"
                                :items="unitsOfMeasuring"
                                :rules="rules"
                                :error-messages="errors.measuring_unit_id"
                                label="Выберите едину измерения"
                                item-title="name"
                                item-value="id">
                            </v-select>
                        </v-col>
                    </v-row>
                </v-container>
            </v-card-text>

            <v-card-actions class="justify-end">
                <v-btn
                    variant="outlined"
                    color="primary"
                    @click="back">
                    Назад
                </v-btn>
                <v-btn
                    variant="tonal"
                    color="primary"
                    @click="save">
                    Сохранить
                </v-btn>
            </v-card-actions>
        </v-form>
    </v-card>

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
