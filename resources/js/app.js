/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue';
import vuetify from "./vuetify.js";
import App from './components/App.vue';
import WSideBar from "./components/WSideBar.vue";
import WTable from "./components/WTable.vue";
import router from "./router/index.js";
import WChildProductsTable from "./components/WChildProductsTable.vue";
import WChildTechCardTable from "./components/WChildTechCardTable.vue";
import WChildDocumentsTable from "./components/WChildDocumentsTable.vue";

/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

const app = createApp(App);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
//     app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
// });

/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */
app
    .component('w-table', WTable)
    .component('w-side-bar', WSideBar)
    .component('w-child-products-table', WChildProductsTable)
    .component("w-child-tech-card-table", WChildTechCardTable)
    .component("w-child-documents-table", WChildDocumentsTable)
    .use(vuetify)
    .use(router)
    .mount('#app');
