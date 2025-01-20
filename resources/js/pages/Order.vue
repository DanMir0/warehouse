<script setup>
import {computed, onMounted, ref} from "vue";
import {useRoute} from "vue-router";
import WChildDocumentsTable from "@/components/WChildDocumentsTable.vue";

const alertMessage = ref("")
const alertType = ref("")
const route = useRoute();
const entity = ref({
    order_status_id: null,
    counterparty_id: null,
    created_at: null,
    updated_at: null,
    finished_at: null,
});
const errors = ref({});
const counterparties = ref([]);
const tab = ref();

const formTitle = computed(() => route.params.id ? "Редактировать заказ" : "Добавить заказ")

onMounted(() => {
    axios.get("/api/counterparties")
        .then(response => {
            counterparties.value = response.data
        })
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
                                disabled
                                v-model="entity.order_status_id"
                                label="Статус производства"
                                :rules="rules"
                                :error-messages="errors.order_status_id">
                            </v-text-field>
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
                                v-model="entity.created_at"
                                label="Дата создания">
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="4">
                            <v-text-field
                                disabled
                                type="date"
                                v-model="entity.updated_at"
                                label="Дата обновления">
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="4">
                            <v-text-field
                                disabled
                                type="date"
                                v-model="entity.finished_at"
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
                                            <w-child-tech-card-table>

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

.custom-tabs .v-tab {
    font-size: 20px;
}

</style>
