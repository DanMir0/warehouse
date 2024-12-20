import { createRouter, createWebHistory } from 'vue-router';
import Products from "../pages/Products.vue";
import Product from "../pages/Product.vue";
import MeasuringUnits from "../pages/MeasuringUnits.vue";
import MeasuringUnit from "../pages/MeasuringUnit.vue";
import OrderStatuses from "../pages/OrderStatuses.vue";
import OrderStatus from "../pages/OrderStatus.vue";
import DocumentTypes from "../pages/DocumentTypes.vue";
import DocumentType from "../pages/DocumentType.vue";
import Counterparties from "@/pages/Counterparties.vue";
import Counterparty from "@/pages/Counterparty.vue";

const routes = [
    {
        path: "/",
        redirect: "/products"
    },
    {
        path: "/products",

        component: Products
    },
    {
        path: "/products/create",
        component: Product,
        props: true,
    },
    {
        path: "/products/:id/edit",
        component: Product,
        props: true,
    },
    {
        path: "/measuring_units",
        component: MeasuringUnits,
    },
    {
        path: "/measuring_units/create",
        component: MeasuringUnit,
        props: true,
    },
    {
        path: "/measuring_units/:id/edit",
        component: MeasuringUnit,
        props: true,
    },
    {
        path: "/order_statuses",
        component: OrderStatuses,
    },
    {
        path: "/order_statuses/create",
        component: OrderStatus,
        props: true,
    },
    {
        path: "/order_statuses/:id/edit",
        component: OrderStatus,
        props: true,
    },
    {
        path: "/document_types",
        component: DocumentTypes,
    },
    {
        path: "/document_types/create",
        component: DocumentType,
        props: true,
    },
    {
        path: "/document_types/:id/edit",
        component: DocumentType,
        props: true,
    },
    {
        path: "/counterparties",
        component: Counterparties,
    },
    {
        path: "/counterparties/create",
        component: Counterparty,
        props: true,
    },
    {
        path: "/counterparties/:id/edit",
        component: Counterparty,
        props: true,
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router
