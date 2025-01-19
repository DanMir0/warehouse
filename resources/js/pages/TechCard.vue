<script setup>
import {computed, onMounted, ref} from "vue";
import {useRoute} from "vue-router";
import router from "../router/index.js";
import WChildProductsTable from "../components/WChildProductsTable.vue";
import {setAlert, compareObjData, formatDate} from "../helpers/helpers.js";

const route = useRoute();

const products = ref();
const selectedProducts = ref([]);
const techCard = ref();

const entity = ref({
    name: null,
    product_id: null,
    created_at: null,
    updated_at: null,
});

const alertMessage = ref("");
const alertType = ref("");
const errors = ref({});

const formTitle = computed(() => route.params.id ? "Редактировать тех карту" : "Добавить тех карту");

const rules = [
    value => {
        if (value) return true

        return "Поле обязательное."
    }
]

function back() {
    router.back();
}

function save() {
    const techCardData = {
        name: entity.value.name,
        product_id: entity.value.product_id,
        products: selectedProducts.value.map(product => ({
            product_id: product.product_id,
            quantity: parseFloat(product.quantity) // Преобразование в число
        })),
        deletedProducts: [...deletedProducts.value],
    }

    const defaultTechCard = {
        name: techCard.value.name,
        product_id: techCard.value.product_id,
        deletedProducts: [],
        products: defaultSelectProducts.value.map(product => ({
            product_id: product.product_id,
            quantity: parseFloat(product.quantity) // Преобразование в число
        }))
    }

    const isUnchanged = compareObjData(defaultTechCard, techCardData)
    if (isUnchanged) {
        setAlert(alertMessage, alertType, "Нечего изменять.", "error");
        return;
    }

    if (techCardData.products.length === 0) {
        setAlert(alertMessage, alertType, "Укажите материалы.", "error");
        return;
    }

    if (route.params.id) {
        axios.put(`/tech_cards/${route.params.id}`, techCardData)
            .then(response => {
                deletedProducts.value = []
                setAlert(alertMessage, alertType, "Тех карта обновлена.", "success");
            })
            .catch(error => {
                if (error.response && error.response.status === 422) {
                    errors.value = {...error.response.data.errors}
                }
                setAlert(alertMessage, alertType, error.message, "error");
            })
    } else {
        axios.post("/tech_cards", techCardData)
            .then(response => {
                setAlert(alertMessage, alertType, "Тех карта добавлена.", "success");
            })
            .catch(error => {
                if (error.response && error.response.status === 422) {
                    errors.value = {...error.response.data.errors}
                }
                setAlert(alertMessage, alertType, error.message, "error");
            });
    }
}

function addProduct(product) {
    selectedProducts.value.push(product);
}

function updatedProduct({id, product}) {
    const index = selectedProducts.value.findIndex(p => p.product_id === id)
    if (index !== -1) {
        selectedProducts.value[index] = {...product}
    }
}

const deletedProducts = ref([])
function deleteProduct(product) {
    deletedProducts.value.push(product.product_id)
    selectedProducts.value = selectedProducts.value.filter(p => p.product_id !== product.product_id)
}

const defaultSelectProducts = ref([])
onMounted(() => {
    axios.get("/api/products")
        .then(response => {
            products.value = response.data;
        })
        .catch(error => {
            console.error(error);
        })
    if (route.params.id) {
        axios.get(`/api/tech_cards/${route.params.id}`)
            .then(response => {
                techCard.value = response.data;
                entity.value = {...techCard.value}
            })
            .catch(error => {
                console.log(error);
            })
        axios.get(`/api/tech_card_products/${route.params.id}`)
            .then(response => {
                selectedProducts.value = response.data
                defaultSelectProducts.value = [...selectedProducts.value]
            })
            .catch(error => {
                console.error(error);
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
                                v-model="entity.product_id"
                                :items="products"
                                :rules="rules"
                                :error-messages="errors.product_id"
                                label="Товар"
                                item-title="name"
                                item-value="id">
                            </v-select>
                        </v-col>
                        <v-col cols="12" sm="6" md="4">
                            <v-text-field
                                type="date"
                                disabled
                                :model-value="formatDate(entity.created_at)"
                                label="Дата создания">
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="4">
                            <v-text-field
                                disabled
                                type="date"
                                :model-value="formatDate(entity.updated_at)"
                                label="Обновленная дата">
                            </v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12">
                            <w-child-products-table
                                :items="selectedProducts"
                                @add-product="addProduct"
                                @updated-product="updatedProduct"
                                @delete-product="deleteProduct">
                            </w-child-products-table>
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
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    border-radius: 50% 20% / 10% 40%;
}
</style>
