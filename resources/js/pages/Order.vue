<script setup>
import {computed, onMounted, ref} from "vue";
import {useRoute} from "vue-router";
import router from "../router/index.js";
import {setAlert} from "../helpers/helpers.js";
import {formatDate} from "../helpers/helpers.js";
import {STATUS_FINISHED, STATUS_IN_PROGRESS, STATUS_NEW} from "../helpers/common/order_statuses.js";

const alertMessage = ref("")
const alertType = ref("")
const route = useRoute();
const entity = ref({
    order_status_id: 1,
    counterparty_id: null,
    created_at: null,
    updated_at: null,
    finished_at: null,
});
const errors = ref({});
const counterparties = ref([]);
const tab = ref();
const orderStatuses = ref([]);
const selectedProducts = ref([]);
const deleteProducts = ref([]);
const defaultSelectedProducts = ref([])

const formTitle = computed(() => route.params.id ? "Редактировать заказ" : "Добавить заказ")

function addProduct(product) {
    selectedProducts.value.push(product)
}

function updatedProduct(product, defaultProduct) {
    const index = selectedProducts.value.findIndex(p => p.product_id === defaultProduct.product_id)
    if (index !== -1) {
        selectedProducts.value[index] = {old_card_tech_id: defaultProduct.tech_card_id,...product}
    }
}

function back() {
    router.back();
}

function save() {
    const orderData = {
        order_status_id: entity.value.order_status_id,
        counterparty_id: entity.value.counterparty_id,
        finished_at: null,
        products: selectedProducts.value.map(product => ({
            order_id: route.params.id ? parseInt(route.params.id) : null,
            tech_card_id: product.tech_card_id,
            old_tech_card_id: product.old_card_tech_id || null,
            quantity: product.quantity
        })),
        deletedProducts: [...deleteProducts.value]
    }

    if (route.params.id) {
        axios.put(`/orders/${route.params.id}`, orderData)
            .then(response => {
                deleteProducts.value = []
                setAlert(alertMessage, alertType, "Заказ обновлен!", "success")
            })
            .catch(error => {
                setAlert(alertMessage, alertType, error.message, "error")
            })
    } else {
        axios.post("/orders", orderData)
            .then(response => {
                setAlert(alertMessage, alertType, "Заказ добавлен.", "success");
            })
            .catch(error => {
                setAlert(alertMessage, alertType, error.message, "error")
            })
    }
}

function setStatusProgress() {

}

function deleteProduct(product) {
    deleteProducts.value.push(product.tech_card_id)
    selectedProducts.value = selectedProducts.value.filter(p => p.product_id !== product.product_id)
}

onMounted(() => {
    axios.get("/api/counterparties")
        .then(response => {
            counterparties.value = response.data
        })
        .catch(error => {
            setAlert(alertMessage, alertType, "Не удалось получить данные о контрагентов.", "error");
        })
    axios.get("/api/order_statuses")
        .then(response => {
            orderStatuses.value = response.data
        })
        .catch(error => {
            setAlert(alertMessage, alertType, "Не удалось получить данные о статусах зазаказ.", "error");
        })
    if (route.params.id) {
        axios.get(`/api/orders/${route.params.id}`)
            .then(response => {
                entity.value = response.data
            })
            .catch(error => {
                setAlert(alertMessage, alertType, error.message, "error");
            })
        axios.get(`/api/order_tech_card/${route.params.id}`)
            .then(response => {
                selectedProducts.value = response.data
                defaultSelectedProducst.value = response.data

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
                            <v-select
                                disabled
                                v-model="entity.order_status_id"
                                label="Статус производства"
                                :items="orderStatuses"
                                item-title="name"
                                item-value="id"
                                :rules="rules"
                                :error-messages="errors.order_status_id">
                            </v-select>
                        </v-col>
                        <v-col cols="12" sm="6" md="4">
                            <v-select
                                v-model="entity.counterparty_id"
                                :items="counterparties"
                                :rules="rules"
                                :error-messages="errors.counterparty_id"
                                label="Контрагент"
                                item-title="name"
                                item-value="id">
                            </v-select>
                        </v-col>
                        <v-col cols="12" sm="6" md="4">
                            <v-text-field
                                disabled
                                type="date"
                                :model-value="formatDate(entity.created_at)"
                                label="Дата создания">
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="4">
                            <v-text-field
                                disabled
                                type="date"
                                :model-value="formatDate(entity.updated_at)"
                                label="Дата обновления">
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="4">
                            <v-text-field
                                disabled
                                type="date"
                                :model-value="formatDate(entity.finished_at)"
                                label="Дата окончания">
                            </v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12">
                            <v-card>
                                <v-tabs
                                    v-model="tab"
                                    bg-color="body-bg"
                                    class="custom-tabs">
                                    <v-tab
                                        value="products"
                                        color="primary">
                                        ТОВАРЫ
                                    </v-tab>
                                    <v-tab
                                        value="documents"
                                        color="primary">
                                        ДОКУМЕНТЫ
                                    </v-tab>
                                </v-tabs>

                                <v-card-text>
                                    <v-tabs-window v-model="tab">
                                        <v-tabs-window-item
                                            value="products">
                                            <w-child-tech-card-table
                                                :items="selectedProducts"
                                                :defaultSelectedProducst="defaultSelectedProducts"
                                                @updated-product="updatedProduct"
                                                @add-product="addProduct"
                                                @delete-products="deleteProduct">

                                            </w-child-tech-card-table>
                                        </v-tabs-window-item>

                                        <v-tabs-window-item
                                            value="documents">
                                            <w-child-documents-table>

                                            </w-child-documents-table>
                                        </v-tabs-window-item>
                                    </v-tabs-window>
                                </v-card-text>
                            </v-card>
                        </v-col>
                    </v-row>


                </v-container>
            </v-card-text>

            <v-card-actions class="justify-end">
                <template v-if="route.params.id">
                    <v-btn
                        v-if="entity.order_status_id === STATUS_NEW"
                        variant="outlined"
                        color="primary"
                        @click="setStatusProgress">
                        В производство
                    </v-btn>
                    <v-btn
                        v-if="entity.order_status_id === STATUS_IN_PROGRESS"
                        variant="outlined"
                        color="primary">
                        Готов
                    </v-btn>
                    <v-btn
                        v-if="entity.order_status_id === STATUS_FINISHED"
                        variant="outlined"
                        color="primary">
                        Выдан
                    </v-btn>
                </template>
                <v-btn
                    variant="outlined"
                    color="primary"
                    @click="back">
                    Назад
                </v-btn>
                <v-btn
                    :disabled="entity.order_status_id !== STATUS_NEW"
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

.custom-tabs .v-tab {
    font-size: 20px;
}

</style>
