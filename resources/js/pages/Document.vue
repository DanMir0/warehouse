<script setup>
import {computed, onMounted, ref} from "vue";
import {useRoute} from "vue-router";
import {setAlert} from "../helpers/helpers.js";
import {fetchDocument, fetchDocumentTypes, fetchCounterparties} from "../services/documenServices.js";
import useFormHandler from "../composoble/useFormHandler.js";

const route = useRoute();
const {alertMessage, alertType, errors, handlerResponse} = useFormHandler()

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

function saveProduct(product) {

}

function save() {

}

onMounted(async () => {
    if (route.params.id) {
        const {success, message, data} = await handlerResponse(fetchDocument(route.params.id));
        setAlert(alertMessage, alertType, message, "error")
        if (success) {
            entity.value = data;
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
                                v-model="entity.document_types_id"
                                label="Выберите тип документа"
                                item-title="name"
                                item-value="id"
                                :items="documentTypes"
                                :rules="rules"
                                :error-messages="errors.document_types_id">
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
                            <w-child-products-table>

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
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    border-radius: 50% 20% / 10% 40%;
}
</style>
<script setup>
</script>
