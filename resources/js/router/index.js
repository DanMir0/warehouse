import { createRouter, createWebHistory } from 'vue-router';
import Products from "../pages/Products.vue";
import Product from "../pages/Product.vue";
import MeasuringUnits from "../pages/MeasuringUnits.vue";
import MeasuringUnit from "../pages/MeasuringUnit.vue";

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
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router
