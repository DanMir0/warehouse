<script setup>
import {computed, onMounted, ref} from "vue";
import {useRoute} from "vue-router";
import {setAlert} from "../helpers/helpers.js";
import {
    fetchDocument,
    fetchDocumentTypes,
    fetchCounterparties,
    fetchDocumentProducts,
    updateDocument,
    addDocument,
} from "../services/documenServices.js";
import useFormHandler from "../composoble/useFormHandler.js";
import router from "../router/index.js";
import WChildProductsTable from "@/components/WChildProductsTable.vue";

const route = useRoute();
const {alertMessage, alertType, errors, handlerResponse} = useFormHandler()
const selectedProducts = ref([]);
const defaultSelectedProducts = ref([]);
const deleteProducts = ref([]);
const entity = ref({
    document_type_id: null,
    counterparty_id: null,
    order_id: null,
});

const documentTypes = ref([]);
const counterparties = ref([]);

const formTitle = computed(() => {
    return route.params.id ? "Редактировать документ" : "Добавить документ"
})

function addProduct(product) {
    selectedProducts.value = [...selectedProducts.value, product]
}

function back() {
    router.back()
}

function deleteProduct(product) {
    deleteProducts.value.push(product.product_id);
    selectedProducts.value = selectedProducts.value.filter(p => p.product_id !== product.product_id);
}

function updatedProduct(product, defaultProduct) {
    console.log(product)
    const index = selectedProducts.value.findIndex(p => p.product_id === defaultProduct.product_id)
    if (index !== -1) {
        selectedProducts.value[index] = {old_product_id: defaultProduct.product_id, ...product.product}
    }
    console.log(selectedProducts.value)
}

async function save() {
    const documentData = {
        document_type_id: entity.value.document_type_id,
        counterparty_id: entity.value.counterparty_id,
        products: selectedProducts.value.map(product => ({
            document_id: route.params.id ? parseInt(route.params.id) : null,
            product_id: product.product_id,
            old_tech_card_id: product.old_product_id || null,
            quantity: product.quantity
        })),
        deletedProducts: [...deleteProducts.value]
    }

    if (route.params.id) {
        const {success, message} = await handlerResponse(updateDocument(route.params.id, documentData))
        setAlert(alertMessage, alertType, success ? "Документ обновлен." : message, success ? "success" : "error");
        if (success) {
            deleteProducts.value = [];
        }
    } else {
        const {success, message} = await handlerResponse(addDocument(documentData))
        setAlert(alertMessage, alertType, success ? "Документ создан." : message, success ? "success" : "error");
    }
}

onMounted(async () => {
    if (route.params.id) {
        const responseDocument = await handlerResponse(fetchDocument(route.params.id));
        setAlert(alertMessage, alertType, responseDocument.message, "error")
        if (responseDocument.success) {
            entity.value = responseDocument.data;
        }

        const responseProductsDocument = await handlerResponse(fetchDocumentProducts(route.params.id))
        setAlert(alertMessage, alertType, responseProductsDocument.message, "error")
        if (responseProductsDocument.success) {
            selectedProducts.value = responseProductsDocument.data;
            defaultSelectedProducts.value = [...selectedProducts.value];
        }
    }

    const responseDocumentTypes = await handlerResponse(fetchDocumentTypes());
    setAlert(alertMessage, alertType, responseDocumentTypes.message, "error")
    if (responseDocumentTypes.success) {
        documentTypes.value = responseDocumentTypes.data;
    }

    const responseCounterparties = await handlerResponse(fetchCounterparties());
    setAlert(alertMessage, alertType, responseCounterparties.message, "error")
    if (responseCounterparties.success) {
        counterparties.value = responseCounterparties.data;
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
                        <v-col cols="12" sm="6" md="2">
                            <v-select
                                v-model="entity.document_type_id"
                                label="Выберите тип документа"
                                item-title="name"
                                item-value="id"
                                :items="documentTypes"
                                :rules="rules"
                                :error-messages="errors.document_type_id">
                            </v-select>
                        </v-col>
                        <v-col cols="12" sm="6" md="2">
                            <v-select
                                v-model="entity.counterparty_id"
                                label="Выберите контрагента"
                                item-title="name"
                                item-value="id"
                                :items="counterparties"
                                :rules="rules"
                                :error-messages="errors.counterparty_id">
                            </v-select>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12">
                            <w-child-products-table
                                :items="selectedProducts"
                                :defaultSelectedProducts="defaultSelectedProducts"
                                @add-product="addProduct"
                                @delete-product="deleteProduct"
                                @updatedProduct="updatedProduct">
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
                    :disabled="entity.order_id"
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
<script setup>
</script>
