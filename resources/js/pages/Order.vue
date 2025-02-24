<script setup>
import {computed, onMounted, ref} from "vue";
import {useRoute} from "vue-router";
import router from "../router/index.js";
import {setAlert} from "../helpers/helpers.js";
import {formatDate} from "../helpers/helpers.js";
import {STATUS_FINISHED, STATUS_IN_PROGRESS, STATUS_NEW, STATUS_ISSUED} from "../helpers/common/order_statuses.js";
import {
    fetchCounterparties,
    fetchOrder,
    fetchOrderStatuses,
    fetchOrderTechCard, postOrder, updateOrder, updateOrderStatus, fetchDocument
} from "../services/orderServices.js";
import useFormHandler from "../composoble/useFormHandler.js";
import {requireRule} from "../helpers/validationRules.js";

defineProps({
    id: [String, Number]
})

const {alertMessage, alertType, errors, handlerResponse} = useFormHandler()

const route = useRoute();
const entity = ref({
    order_status_id: 1,
    counterparty_id: null,
    created_at: null,
    updated_at: null,
    finished_at: null,
});
const counterparties = ref([]);
const tab = ref();
const orderStatuses = ref([]);
const selectedProducts = ref([]);
const deleteProducts = ref([]);
const dialog = ref(false);
const dialogMessage = ref("");
const orderStatus = ref(null);
const documents = ref([]);

const formTitle = computed(() => route.params.id ? "Редактировать заказ" : "Добавить заказ")

function openDialog(status, message) {
    orderStatus.value = status;
    dialogMessage.value = message;
    dialog.value = true;
}

async function confirmAction() {
    if (!orderStatus.value) return;

    const response = await handlerResponse(updateOrderStatus(route.params.id, { order_status_id: orderStatus.value }));

    if (!response.success) {

        if (response.details) {
            let errorText = response.message + "\n";

            // Проверяем, начинается ли строка с "Недостаточно материалов:"
            if (typeof response.details === "string" && response.details.startsWith("Недостаточно материалов:")) {
                try {
                    // Извлекаем JSON из строки
                    const jsonStart = response.details.indexOf("["); // Ищем, где начинается JSON
                    const materials = JSON.parse(response.details.slice(jsonStart)); // Парсим JSON

                    materials.forEach(material => {
                        errorText += `Товар: ${material.product_name}, нехватка: ${material.quantity}\n`;
                    });
                } catch (e) {
                    errorText += "\nНе удалось обработать детали ошибки.";
                }
            }

            alertMessage.value = errorText; // Выводим сообщение в UI
        } else {
            alertMessage.value = response.message; // Общая ошибка
        }
        return;
    } else {
        entity.value.order_status_id = response.data.order.order_status_id;
    }
    dialog.value = false;

}

function addProduct(product) {
    let isSelectedProduct = selectedProducts.value.findIndex(p => p.product_id === product.product_id)
    if (!isSelectedProduct) {
        setAlert(alertMessage, alertType, "Вы уже добавили этот товар.", "error")
    } else {
        selectedProducts.value.push(product)
    }
}

function updatedProduct(product, defaultProduct) {
    const index = selectedProducts.value.findIndex(p => p.product_id === defaultProduct.product_id)
    if (index !== -1) {
        selectedProducts.value[index] = {old_card_tech_id: defaultProduct.tech_card_id, ...product}
    }
    console.log(selectedProducts.value)
}

function back() {
    router.back();
}

async function save() {
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
        const {success, message} = await handlerResponse(updateOrder(route.params.id, orderData));
        setAlert(alertMessage, alertType, success ? "Заказ обновлен." : message, success ? "success" : "error");
        if (success) {
            deleteProducts.value = [];
        }
    } else {
        const {success, message} = await handlerResponse(postOrder(orderData));
        setAlert(alertMessage, alertType, success ? "Заказ добавлен." : message, success ? "success" : "error");
    }
}

function deleteProduct(product) {
    deleteProducts.value.push(product.tech_card_id)
    selectedProducts.value = selectedProducts.value.filter(p => p.product_id !== product.product_id)
}

onMounted(async () => {
    const responseCounterparties = await handlerResponse(fetchCounterparties());
    setAlert(alertMessage, alertType, responseCounterparties.message, "error");
    if (responseCounterparties.success) {
        counterparties.value = responseCounterparties.data;
    }

    const responseOrderStatuses = await handlerResponse(fetchOrderStatuses());
    setAlert(alertMessage, alertType, responseOrderStatuses.message, "error");
    if (responseOrderStatuses.success) {
        orderStatuses.value = responseOrderStatuses.data;
    }

    if (route.params.id) {
        const responseOrder = await handlerResponse(fetchOrder(route.params.id));
        setAlert(alertMessage, alertType, responseOrder.message, "error");
        if (responseOrder.success) {
            entity.value = responseOrder.data
        }

        const responseOrderTechCard = await handlerResponse(fetchOrderTechCard(route.params.id));
        setAlert(alertMessage, alertType, responseOrderTechCard.message, "error");
        if (responseOrderTechCard.success) {
            selectedProducts.value = responseOrderTechCard.data;
        }

        const responseDocumentOrder = await handlerResponse(fetchDocument(route.params.id))
        setAlert(alertMessage, alertType, responseDocumentOrder.message, "error");
        if (responseDocumentOrder.success) {
            documents.value = responseDocumentOrder.data.map(document => ({
                ...document,
                created_at: formatDate(document.created_at)
            }))
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
                            <v-select
                                disabled
                                v-model="entity.order_status_id"
                                label="Статус производства"
                                :items="orderStatuses"
                                item-title="name"
                                item-value="id"
                                :rules="[requireRule]"
                                :error-messages="errors.order_status_id">
                            </v-select>
                        </v-col>
                        <v-col cols="12" sm="6" md="4">
                            <v-select
                                v-model="entity.counterparty_id"
                                :items="counterparties"
                                :rules="[requireRule]"
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
                                                @updated-product="updatedProduct"
                                                @add-product="addProduct"
                                                @delete-products="deleteProduct">
                                            </w-child-tech-card-table>
                                        </v-tabs-window-item>

                                        <v-tabs-window-item
                                            value="documents">
                                            <w-child-documents-table
                                                :items="documents">
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
                        @click="openDialog(STATUS_IN_PROGRESS, 'Проверьте свой заказ. Потом отрпавления в производсво, заказ нельзуя будет изменить.')">
                        В производство
                    </v-btn>
                    <v-btn
                        v-if="entity.order_status_id === STATUS_IN_PROGRESS"
                        variant="outlined"
                        color="primary"
                        @click="openDialog(STATUS_FINISHED, 'Заказ готов? Подтвердите завершение.')">
                        Готов
                    </v-btn>
                    <v-btn
                        v-if="entity.order_status_id === STATUS_FINISHED"
                        variant="outlined"
                        color="primary"
                        @click="openDialog(STATUS_ISSUED, 'Выдать заказ клиенту?')">
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
            <v-dialog
                v-model="dialog"
                max-width="500"
                persistent>
                <v-card class="centered-text" title="Подтверждение действия">
                    <v-card-text class="centered-text">{{ dialogMessage }}</v-card-text>
                    <v-card-actions class="justify-space-between">
                        <v-btn @click="dialog = false">Отмена</v-btn>
                        <v-btn color="primary" @click="confirmAction">Подтвердить</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
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

.custom-tabs .v-tab {
    font-size: 20px;
}

.centered-text {
    text-align: center;
}
</style>
