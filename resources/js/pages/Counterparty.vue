<script setup>
import {useRoute} from "vue-router";
import {computed, onMounted, ref} from "vue";
import router from "../router/index.js";
import {setAlert} from "../helpers/helpers.js";
import {requireRule, innRule} from "../helpers/validationRules.js";
import {formatDate, formatPhoneForDisplay, formatPhoneForServer} from "../helpers/helpers.js";
import useFormHandler from "../composoble/useFormHandler.js";
import {fetchCounterparty, postCounterparty, updateCounterparty} from "../services/counterpartyService.js";

const {alertMessage, alertType, errors, handlerResponse} = useFormHandler()

const route = useRoute();

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
    entity.value.contact_info = formatPhoneForDisplay(event.target.value)
}

function back() {
    router.back();
}

async function save() {
    errors.value = {};
    alertMessage.value = '';
    entity.value.contact_info = formatPhoneForServer(entity.value.contact_info)

    if (route.params.id) {
        const {success, message} = await handlerResponse(updateCounterparty(route.params.id, entity.value));
        setAlert(alertMessage, alertType, success ? "Контрагент изменен." : message, success ? "success" : "error");
    } else {
        const {success, message} = await handlerResponse(postCounterparty(entity.value));
        setAlert(alertMessage, alertType, success ? "Контрагент добавлен." : message, success ? "success" : "error");
    }
}

onMounted(async () => {
        if (route.params.id) {
            const {success, message, data} = await handlerResponse(fetchCounterparty(route.params.id));
            setAlert(alertMessage, alertType, message, "error");
            if (success) {
                entity.value = data;
                if (entity.value.contact_info) {
                    entity.value.contact_info = formatPhoneForDisplay(entity.value.contact_info)
                }
            }
        }
    }
)
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
    position: fixed;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    border-radius: 8px;
}
</style>
