<script setup>
import {ref} from "vue";
import {useRoute} from "vue-router";

const route = useRoute();

const items = ref([
    {title: "Товары", icon: "mdi-shopping", to: "/products"},
    {
        title: "Контрагенты",
        icon: "mdi-account-group",
        to: "/counterparties",
    },
    {
        title: "Документы",
        icon: "mdi-file-document-outline",
        to: "/documents",
    },
    {title: "Заказы", icon: "mdi-chart-line", to: "/orders"},
    {title: "Тех карты", icon: "mdi-credit-card", to: "/tech_cards"},
    {title: "Настройки", icon: "mdi-cog-outline", to: "/settings"},
]);

const handbook = ref([
    {
        title: "Единицы измерения",
        icon: "mdi-beaker-outline",
        to: "/measuring_units",
    },
    {
        title: "Статусы производства",
        icon: "mdi-clipboard-pulse-outline",
        to: "/order_statuses",
    },
    {
        title: "Типы документов",
        icon: "mdi-file-document-multiple-outline",
        to: "/document_types",
    },
]);

const isActive = (path) => {
    return route.path.startsWith(path);
}
</script>

<template>
    <v-navigation-drawer class="side-bar" :width="260" permanent>
        <v-list-item prepend-icon="mdi-store" color="purple-darken-2" title="Склад" subtitle="Версия 2.0"></v-list-item>
        <v-divider></v-divider>
        <v-list nav color="primary">
            <v-list-item
                v-for="item in items"
                :key="item.title"
                :to="item.to"
                :prepend-icon="item.icon"
                link
                :active="isActive(item.to)"
                active-class="primary white--text"
            >
                {{ item.title }}
            </v-list-item>
            <v-list-group prepend-icon="mdi-book-open-page-variant-outline">
                <template v-slot:activator="{ props }">
                    <v-list-item
                        class="h5"
                        v-bind="props"
                        title="Справочник"
                    ></v-list-item>
                </template>

                <v-list-item
                    class="v-list--nav"
                    v-for="item in handbook"
                    :key="item.title"
                    :to="item.to"
                    :prepend-icon="item.icon"
                    link
                    :active="isActive(item.to)"
                    active-class="primary white--text"
                >
                    {{ item.title }}
                </v-list-item>
            </v-list-group>
        </v-list>
    </v-navigation-drawer>
</template>

<style scoped>
.side-bar {
    .v-list--nav {
        padding-left: 5px !important;
    }
}
</style>
