<script setup>
import {useRoute} from "vue-router";
import {computed, onMounted, ref} from "vue";
import router from "../router/index.js";

const route = useRoute();

const measuring_unit = ref()

const entity = ref({
    id: null,
    name: null,
});

const alertMessage = ref('');
const alertType = ref('');

const rules = [
    value => {
        if (value) return true

        return "Поле обязательное."
    }
]

const formTitle = computed(() => {
    return route.params.id ? "Редактировать единицу измерения" : "Добавить единицу измерения";
})

onMounted(() => {
    if (route.params.id) {
        axios.get(`/api/measuring_units/${route.params.id}`)
            .then(response => {
                measuring_unit.value = response.data
                console.log(response.data)
                entity.value = {...measuring_unit.value}
            })
            .catch(error => {
                alertMessage.value = error.message;
                alertType.value = "error";
            });
    }
})

function back() {
    router.back()
}

function save() {
    if (route.params.id) {
        axios.put(`/measuring_units/${route.params.id}`, entity.value)
            .then(response => {
                alertMessage.value = "Единица измерения изменена.";
                alertType.value = "success";
            })
            .catch(error => {
                alertMessage.value = error.message;
                alertType.value = "error";
            });
    } else {
        axios.post("/measuring_units", entity.value)
            .then(response => {
                alertMessage.value = "Единица измерения добавлена.";
                alertType.value = "success";
            })
            .catch(error => {
                alertMessage.value = error.message;
                alertType.value = "error";
            })
    }
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
                            >
                            </v-text-field>
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
