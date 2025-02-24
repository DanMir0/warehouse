<script setup>
import {computed, onMounted, ref} from "vue";
import {useRoute} from "vue-router";
import router from "../router/index.js";
import WChildProductsTable from "../components/WChildProductsTable.vue";
import {setAlert, compareObjData, formatDate} from "../helpers/helpers.js";
import {requireRule} from "../helpers/validationRules.js";
import {fetchProducts, fetchTechCard, fetchTechCardProduct, postTechCard, updateTechCard} from "../services/tehcCardServices.js";
import useFormHandler from "../composoble/useFormHandler.js";

defineProps({
    id: [String, Number]
})

const {alertMessage, alertType, errors, handlerResponse} = useFormHandler()
const route = useRoute();
const products = ref();
const selectedProducts = ref([]);
const techCard = ref();
const defaultSelectedProducts = ref([]);
const deletedProducts = ref([]);

const entity = ref({
    name: null,
    product_id: null,
    created_at: null,
    updated_at: null,
});

const formTitle = computed(() => route.params.id ? "Редактировать тех карту" : "Добавить тех карту");

function back() {
    router.back();
}

async function save() {
    const techCardData = {
        name: entity.value.name,
        product_id: entity.value.product_id,
        products: selectedProducts.value.map(product => ({
            product_id: product.product_id,
            quantity: parseFloat(product.quantity) // Преобразование в число
        })),
        deletedProducts: [...deletedProducts.value],
    }

    if (techCardData.products.length === 0) {
        setAlert(alertMessage, alertType, "Укажите материалы.", "error");
        return;
    }

    if (route.params.id) {
        const defaultTechCard = {
            name: techCard.value.name,
            product_id: techCard.value.product_id,
            deletedProducts: [],
            products: defaultSelectedProducts.value.map(product => ({
                product_id: product.product_id,
                quantity: parseFloat(product.quantity) // Преобразование в число
            }))
        }

        const isUnchanged = compareObjData(defaultTechCard, techCardData)
        if (isUnchanged) {
            setAlert(alertMessage, alertType, "Нечего изменять.", "error");
            return;
        }

        const {success, message} = await handlerResponse(updateTechCard(route.params.id, techCardData))
        setAlert(alertMessage, alertType, success ? "Тех карта обновлена." : message, success ? "success" : "error");
        if (success)
        {
            deletedProducts.value = [];
        }
    } else {
        const {success, message} = await handlerResponse(postTechCard(techCardData))
        setAlert(alertMessage, alertType, success ? "Тех карта добавлена." : message, success ? "success" : "error");
    }
}

function addProduct(product) {
    selectedProducts.value = [...selectedProducts.value, product]
}

function updatedProduct({id, product}) {
    const index = selectedProducts.value.findIndex(p => p.product_id === id)
    if (index !== -1) {
        selectedProducts.value[index] = {...product}
    }
}

function deleteProduct(product) {
    deletedProducts.value.push(product.product_id)
    selectedProducts.value = selectedProducts.value.filter(p => p.product_id !== product.product_id)
}

onMounted(async () => {
    const productResponse = await handlerResponse(fetchProducts())
    setAlert(alertMessage, alertType, productResponse.message, "error");
    if (productResponse.success) {
        products.value = productResponse.data;
    }

    if (route.params.id) {
        const techCardResponse = await handlerResponse(fetchTechCard(route.params.id));
        setAlert(alertMessage, alertType, techCardResponse.message, "error");
        if (techCardResponse.success) {
            techCard.value = techCardResponse.data;
            entity.value = {...techCard.value};
        }

        const productCardResponse = await handlerResponse(fetchTechCardProduct(route.params.id));
        setAlert(alertMessage, alertType, productCardResponse.message, "error");
        if (productCardResponse.success) {
            selectedProducts.value = productCardResponse.data;
            defaultSelectedProducts.value = [...selectedProducts.value]
        }
    }
})
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
                                :rules="[requireRule]"
                                :error-messages="errors.name">
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="4">
                            <v-select
                                v-model="entity.product_id"
                                :items="products"
                                :rules="[requireRule]"
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
                                :defaultSelectedProducts="defaultSelectedProducts"
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
    position: fixed;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    border-radius: 8px;
}
</style>
