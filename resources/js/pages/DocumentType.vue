<script setup>
import {computed, onMounted, ref} from "vue";
import {useRoute} from "vue-router";
import router from "../router/index.js";
import {setAlert} from "../helpers/helpers.js";
import {requireRule} from "../helpers/validationRules.js";
import useFormHandler from "../composoble/useFormHandler.js";
import {fetchDocumentType, postDocumentType, putDocumentType} from "../services/documentTypeService.js";

const {alertMessage, alertType, errors, handlerResponse} = useFormHandler()

const route = useRoute();

const entity = ref({
    name: null,
    in_out: null,
});

const inOut = ref([
    {id: 1, name: "Приход", value: "IN"},
    {id: 2, name: "Расход", value: "OUT"},
])

const rules = [requireRule]

const formTitle = computed(() => {
    return route.params.id ? "Редактировать тип документа" : "Добавить тип документа";
})

function back() {
    router.back();
}

async function save() {
    if (route.params.id) {
        const {success, message} = await handlerResponse(putDocumentType(route.params.id, entity.value));
        setAlert(alertMessage, alertType, success ? "Тип документа обновлен." : message, success ? "success" : "error");
    } else {
        const {success, message} = await handlerResponse(postDocumentType(entity.value));
        setAlert(alertMessage, alertType, success ? "Тип документа добавлен." : message, success ? "success" : "error");
    }
}

onMounted(async () => {
    if (route.params.id) {
        const {success, message, data} = await handlerResponse(fetchDocumentType(route.params.id));
        setAlert(alertMessage, alertType, message, "error")
        if (success) {
            entity.value = data;
        }
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
                                :rules="rules"
                                :error-messages="errors.name">
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="4">
                            <v-select
                                v-model="entity.in_out"
                                :items="inOut"
                                :rules="rules"
                                label="Выберите тип документа"
                                item-title="name"
                                item-value="value"
                                :error-messages="errors.in_out">
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
