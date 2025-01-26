<script setup>
import {useRoute} from "vue-router";
import {computed, onMounted, ref} from "vue";
import router from "../router/index.js";
import {setAlert} from "../helpers/helpers.js";
import {requireRule, innRule} from "../helpers/validationRules.js";
import {formatDate, formatPhone} from "../helpers/helpers.js";

const route = useRoute();

const counterparty = ref();

const alertMessage = ref('');
const alertType = ref('');
const errors = ref({});

const entity = ref({
    name: null,
    contact_info: null,
    address: null,
    inn: null,
    contact_persons: null,
    created_at: null,
    updated_at: null,
});

const rules = [requireRule];

const formTitle = computed(() => {
    return route.params.id ? "Редактировать контрагента" : "Добавить контрагента";
})

function onPhoneInput(event) {
    entity.value.contact_info = formatPhone(event.target.value)
}

function back() {
    router.back();
}

function save() {
    errors.value = {};
    alertMessage.value = '';

    if (route.params.id) {
        axios.put(`/counterparties/${route.params.id}`, entity.value)
            .then(response => {
                setAlert(alertMessage, alertType, "Контрагент изменен.", "success");
            })
            .catch(error => {
                if (error.response && error.response.status === 422) {
                    errors.value =  {...error.response.data.errors};
                }
                setAlert(alertMessage, alertType, error.message, "error");
            })
    } else {
        axios.post("/counterparties", entity.value)
            .then(response => {
                setAlert(alertMessage, alertType, "Контрагент добавлен.", "success");
            })
            .catch(error => {
                if (error.response && error.response.status === 422) {
                    errors.value =  {...error.response.data.errors};
                }
                setAlert(alertMessage, alertType, error.message, "error");
            })
    }
}

onMounted(() => {
    if (route.params.id) {
        axios.get(`/api/counterparties/${route.params.id}`)
            .then(response => {
                counterparty.value = response.data;
                entity.value = {...counterparty.value};

                if (entity.value.contact_info) {
                    entity.value.contact_info = formatPhone(entity.value.contact_info)
                }
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
                                :error-messages="errors.name || []">
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="4">
                            <v-text-field
                                type="tel"
                                v-model="entity.contact_info"
                                label="Контактная информация"
                                :rules="rules"
                                :error-messages="errors.contact_info || []"
                                @input="onPhoneInput">
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="4">
                            <v-text-field
                                v-model="entity.address"
                                label="Адресс"
                                :rules="rules"
                                :error-messages="errors.address || []">
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="4">
                            <v-text-field
                                v-model="entity.inn"
                                label="ИНН"
                                :rules="[rules, innRule]"
                                :error-messages="errors.inn || []"
                                @input="entity.inn = entity.inn.replace(/\D/g, '').slice(0, 10)">
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="4">
                            <v-text-field
                                v-model="entity.contact_persons"
                                label="Контактное лицо"
                                :rules="rules"
                                :error-messages="errors.contact_persons || []">
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="4">
                            <v-text-field
                                disabled
                                type="date"
                                :model-value="formatDate(entity.created_at)"
                                label="Дата создания"
                                :rules="rules">
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="4">
                            <v-text-field
                                disabled
                                type="date"
                                :model-value="formatDate(entity.updated_at)"
                                label="Обновленная дата"
                                :rules="rules">
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
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    border-radius: 50% 20% / 10% 40%;
}
</style>
