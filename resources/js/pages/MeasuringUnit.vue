<script setup>
import {useRoute} from "vue-router";
import {computed, onMounted, ref} from "vue";
import router from "../router/index.js";
import {setAlert} from "../helpers/helpers.js";
import {requireRule} from "../helpers/validationRules.js";
import useFormHandler from "../composoble/useFormHandler.js";
import {fetchMeasuringUnit, updateMeasuringUnit, postMeasuringUnit} from "../services/measuringUnitService.js";

defineProps({
    id: [String, Number]
})

const {alertMessage, alertType, handlerResponse, errors} = useFormHandler()

const route = useRoute();

const entity = ref({
    id: null,
    name: null,
});

const formTitle = computed(() => {
    return route.params.id ? "Редактировать единицу измерения" : "Добавить единицу измерения";
})

function back() {
    router.back();
}

async function save() {
    if (route.params.id) {
        const {success, message} = await handlerResponse(updateMeasuringUnit(route.params.id, entity.value));
        setAlert(alertMessage, alertType, success ? "Единица измерения обновлена." : message, success ? "success" : "error");
    } else {
        const {success, message, data} = await handlerResponse(postMeasuringUnit(entity.value));
        setAlert(alertMessage, alertType, success ? "Единица измерения добавлена." : message, success ? "success" : "error");

        await router.push(`/measuring_units/${data}/edit`)
    }

}

onMounted(async () => {
    if (route.params.id) {
        const {success, message, data} = await handlerResponse(fetchMeasuringUnit(route.params.id));
        setAlert(alertMessage, alertType, message, "error");
        if (success) {
            entity.value = {...data};
        }
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
                                label="Код">
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="4">
                            <v-text-field
                                v-model="entity.name"
                                label="Наименование"
                                :rules="[requireRule]"
                                :error-messages="errors.name">
                            </v-text-field>
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
