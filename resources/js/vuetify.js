import { createVuetify } from "vuetify";
import * as components from 'vuetify/components'; // Импорт всех компонентов
import * as directives from 'vuetify/directives'; // Импорт всех директив
import 'vuetify/styles'; // Импорт стилей Vuetify
import '@mdi/font/css/materialdesignicons.css';

const vuetify = createVuetify({
    icons: {
      defaultSet: 'mdi',
    },
    components,
    directives,
});

export default vuetify;
