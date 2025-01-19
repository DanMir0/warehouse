<script setup>
import {computed, onMounted, ref} from "vue";
import {useRoute} from "vue-router";
import router from "../router/index.js";
import {setAlert} from "../helpers/helpers.js";

const route = useRoute();

const documentType = ref();

const alertMessage = ref('');
const alertType = ref('');
const errors = ref({})

const entity = ref({
    name: null,
    in_out: null,
});

const inOut = ref([
    {id: 1, name: "Приход", value: "IN"},
    {id: 2, name: "Расход", value: "OUT"},
])

const rules = [
    value => {
        if (value) return true

        return "Поле обязательное."
    }
]

const formTitle = computed(() => {
    return route.params.id ? "Редактировать тип документа" : "Добавить тип документа";
})

function back() {
    router.back();
}

function save() {
    if (route.params.id) {
        axios.put(`/document_types/${route.params.id}`, entity.value)
            .then(response => {
                setAlert(alertMessage, alertType, "Тип документа изменен.", "success");
            })
            .catch(error => {
                if (error.response && error.response.status === 422) {
                    errors.value = {...error.response.data.errors}
                }
                setAlert(alertMessage, alertType, error.message, "error");
            })
    } else {
        axios.post("/document_types", entity.value)
            .then(response => {
                setAlert(alertMessage, alertType, "Тип документа добавлен.", "success");
            })
            .catch(error => {
                if (error.response && error.response.status === 422) {
                    errors.value = {...error.response.data.errors}
                }
                setAlert(alertMessage, alertType, error.message, "error");
            })
    }
}

onMounted(() => {
    if (route.params.id) {
        axios.get(`/api/document_types/${route.params.id}`)
            .then(response => {
                documentType.value = response.data;
                entity.value = {...documentType.value}
            })
            .catch(error => {
                setAlert(alertMessage, alertType, error.message, "error");
            })
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
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    border-radius: 50% 20% / 10% 40%;
}
</style>
