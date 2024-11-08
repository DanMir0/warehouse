<script setup>
import {useRoute} from "vue-router";
import {computed, onMounted, ref} from "vue";
import router from "../router/index.js";

const alertMessage = ref('');
const alertType = ref('');

const route = useRoute();

const orderStatus = ref([])

const entity = ref({
    name: null,
});

const rules = [
    value => {
        if (value) return true

        return "Поле обязательное"
    }
]

const formTitle = computed(() => {
    return route.params.id ? "Редактировать статус заказа" : "Добавить статус заказа";
})

function save() {
    if (route.params.id) {
        axios.put(`/order_statuses/${route.params.id}`, entity.value)
            .then(response => {
                alertMessage.value = "Статус заказа изменен.";
                alertType.value = "success";
            })
            .catch(error => {
                alertMessage.value = error.message;
                alertType.value = "error";
            })
    } else {
        axios.post("/order_statuses", entity.value)
            .then(response => {
                alertMessage.value = "Статус заказа добавлен.";
                alertType.value = "success";
            })
            .catch(error => {
                alertMessage.value = error.message;
                alertType.value = "error";
            })
    }
}

onMounted(() => {
    if (route.params.id) {
        axios.get(`/api/order_statuses/${route.params.id}`)
            .then(response => {
                orderStatus.value = response.data;
                entity.value = {...orderStatus.value};
            })
            .catch(error => {
                alertMessage.value = error.message;
                alertType.value = "error";
            })
    }
})

function back() {
    router.back();
}
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
                                label="Код"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="4">
                            <v-text-field
                                v-model="entity.name"
                                label="Наименование"
                                :rules="rules"
                            ></v-text-field>
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
