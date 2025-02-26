<script setup>
import {onMounted, ref} from "vue";
import {setAlert} from "../helpers/helpers.js";
import {fetchCounterparties} from "../services/counterpartyService.js";
import useFormHandler from "../composoble/useFormHandler.js";
import {postSetting, getSettings} from "../services/settingServices.js";

const {alertMessage, alertType, errors, handlerResponse} = useFormHandler()

const settings = ref({
    PRODUCTION_HALL: null,
    CUSTOMER_ID: null,
})
const counterparties = ref([])

async function save() {
    // Создаем массивы key и value из объекта settings
    const keys = Object.keys(settings.value); // Получаем все ключи
    const values = keys.map((key) => settings.value[key]); // Получаем соответствующие значения

    // Формируем payload
    const payload = {
        key: keys,
        value: values,
    };

    console.log(payload); // Печатаем payload для отладки

    // Отправляем данные
    const { success, message, data } = await handlerResponse(postSetting(payload));
    setAlert(
        alertMessage,
        alertType,
        success ? "Настройки обновлены" : message,
        success ? "success" : "error"
    );
}


async function loadSettings() {
    const { success, message, data } = await handlerResponse(getSettings());

    if (success) {
        console.log("Загруженные настройки:", data);
        // Преобразуем id -> name
        data.forEach((setting) => {
            const counterparty = counterparties.value.find((c) => c.id == setting.value);
            settings.value[setting.key] = counterparty ? counterparty.name : null;
        });
    } else {
        setAlert(alertMessage, alertType, message, "error");
    }
}

onMounted(async () => {
    const responseCounterparties = await handlerResponse(fetchCounterparties());
    setAlert(alertMessage, alertType, responseCounterparties.message, "error");
    if (responseCounterparties.success) counterparties.value = responseCounterparties.data;

    await loadSettings()
})
</script>

<template>
    <v-card>
        <v-form>
            <v-card-text>
                <v-container>
                    <v-row>
                        <v-col cols="12" sm="6" md="4">
                            <!--                            :rules="[$rules.required]"-->
                            <v-select
                                v-model="settings.PRODUCTION_HALL"
                                :items="counterparties"
                                label="Производственный цех"
                                item-title="name"
                                item-value="id"
                            >
                            </v-select>
                        </v-col>
                        <v-col cols="12" sm="6" md="4">
                            <v-select
                                v-model="settings.CUSTOMER_ID"
                                :items="counterparties"
                                label="Название организации"
                                item-title="name"
                                item-value="id"
                            >
                            </v-select>
                        </v-col>
                    </v-row>
                </v-container>
            </v-card-text>
            <v-card-actions>
                <v-spacer/>
                <v-btn color="primary" @click="save">Сохранить</v-btn>
            </v-card-actions>
        </v-form>
    </v-card>
</template>

<style scoped>

</style>
