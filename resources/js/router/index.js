import { createRouter, createWebHistory } from 'vue-router';
import Products from "../pages/Products.vue";
import Product from "../pages/Product.vue";

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
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router
