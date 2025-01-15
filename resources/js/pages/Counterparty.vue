<script setup>
import {useRoute} from "vue-router";
import {computed, onMounted, ref} from "vue";
import router from "../router/index.js";
import {en} from "vuetify/locale";

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

const requiredRule = value => !!value || "Поле обязательное.";
const innRule = value => /^[0-9]{10}$/.test(value) || "ИНН должен содержать 10 цифр.";

const formTitle = computed(() => {
    return route.params.id ? "Редактировать контрагента" : "Добавить контрагента";
})

function back() {
    router.back();
}

function save() {
    errors.value = {};
    alertMessage.value = '';

    if (route.params.id) {
        axios.put(`/counterparties/${route.params.id}`, entity.value)
            .then(response => {
                alertMessage.value = "Контрагент изменен";
                alertType.value = "success";
            })
            .catch(error => {
                if (error.response && error.response.status === 422) {
                    errors.value =  {...error.response.data.errors};
                }
                alertMessage.value = error.message;
                alertType.value = "error";
            })
    } else {
        axios.post("/counterparties", entity.value)
            .then(response => {
                alertMessage.value = "Контрагент добавлен"
                alertType.value = "success"
            })
            .catch(error => {
                if (error.response && error.response.status === 422) {
                    errors.value =  {...error.response.data.errors};
                }
                alertMessage.value = error.message;
                alertType.value = "error";
            })
    }
}

function formatDate(date) {
    if (!date) return "";
    return new Date(date).toISOString().split('T')[0];
}

function formatPhone(event) {
    let value = event.target.value.replace(/\D/g, "")
    if (value.startsWith("7")) {
        value = "+" + value
    } else {
        value = "+7" + value;
    }

    if (value.length > 2) value = value.slice(0, 2) + " (" + value.slice(2);
    if (value.length > 7) value = value.slice(0, 7) + ") " + value.slice(7);
    if (value.length > 12) value = value.slice(0, 12) + "-" + value.slice(12);
    if (value.length > 15) value = value.slice(0, 15) + "-" + value.slice(15);
    if (value.length > 18) value = value.slice(0, 18);

    entity.value.contact_info = value;
}

onMounted(() => {
    if (route.params.id) {
        axios.get(`/api/counterparties/${route.params.id}`)
            .then(response => {
                counterparty.value = response.data;
                entity.value = {...counterparty.value};
            })
            .catch(error => {
                alertMessage.value = error.message;
                alertType.value = "error";
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
                                :rules="[requiredRule]"
                                :error-messages="errors.name || []">
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="4">
                            <v-text-field
                                type="tel"
                                v-model="entity.contact_info"
                                label="Контактная информация"
                                :rules="[requiredRule]"
                                :error-messages="errors.contact_info || []"
                                @input="formatPhone">
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="4">
                            <v-text-field
                                v-model="entity.address"
                                label="Адресс"
                                :rules="[requiredRule]"
                                :error-messages="errors.address || []">
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="4">
                            <v-text-field
                                v-model="entity.inn"
                                label="ИНН"
                                :rules="[requiredRule, innRule]"
                                :error-messages="errors.inn || []"
                                @input="entity.inn = entity.inn.replace(/\D/g, '').slice(0, 10)">
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="4">
                            <v-text-field
                                v-model="entity.contact_persons"
                                label="Контактное лицо"
                                :rules="[requiredRule]"
                                :error-messages="errors.contact_persons || []">
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="4">
                            <v-text-field
                                disabled
                                type="date"
                                :model-value="formatDate(entity.created_at)"
                                label="Дата создания"
                                :rules="[requiredRule]">
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="4">
                            <v-text-field
                                disabled
                                type="date"
                                :model-value="formatDate(entity.updated_at)"
                                label="Обновленная дата"
                                :rules="[requiredRule]">
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
