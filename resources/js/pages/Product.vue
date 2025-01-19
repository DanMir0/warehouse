<script setup>
import {computed, onMounted, ref} from "vue";
import router from "../router/index.js";
import {useRoute} from "vue-router";
import {setAlert} from "../helpers/helpers.js";

const route = useRoute();

const product = ref();

const entity = ref({
    name: null,
    measuring_unit_id: null,
});

const alertMessage = ref('');
const alertType = ref('');
const errors = ref({});

const unitsOfMeasuring = ref([]);

onMounted(() => {
    axios.get("/api/measuring_units").then(response => {
        unitsOfMeasuring.value = response.data;
    })
        .catch(e => {
            console.log(e);
        })
    if (route.params.id) {
        axios.get(`/api/product/${route.params.id}`)
            .then(response => {
                product.value = response.data
                entity.value = {...product.value}
            })
            .catch(error => {
                setAlert(alertMessage, alertType, error.message, "error");
            });
    }
});

function save() {
    if (route.params.id) {
        axios.put(`/products/${route.params.id}`, entity.value)
            .then(response => {
                setAlert(alertMessage, alertType, "Товар изменен.", "success");;
            })
            .catch(error => {
                if (error.response && error.response.status === 422) {
                    errors.value = {...error.response.data.errors}
                }
                setAlert(alertMessage, alertType, error.message, "error");
            });
    } else {
        axios.post("/products", entity.value)
            .then(response => {
                setAlert(alertMessage, alertType, "Товар добавлен.", "success");;
            })
            .catch(error => {
                if (error.response && error.response.status === 422) {
                    errors.value = {...error.response.data.errors}
                }
                setAlert(alertMessage, alertType, error.message, "error");
            });
    }
}

function back() {
    router.back();
}

const rules = [
    value => {
        if (value) return true

        return "Поле обязательное."
    }
];

const formTitle = computed(() => {
    return route.params.id ? "Редактировать товар" : "Добавить товар"
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
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    border-radius: 50% 20% / 10% 40%;
}
</style>
