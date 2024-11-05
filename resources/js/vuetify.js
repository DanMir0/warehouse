import { createVuetify } from "vuetify";
import * as components from 'vuetify/components'; // Импорт всех компонентов
import * as directives from 'vuetify/directives'; // Импорт всех директив
import 'vuetify/styles'; // Импорт стилей Vuetify
import '@mdi/font/css/materialdesignicons.css';

const vuetify = createVuetify({
    icons: {
      defaultSet: 'mdi',
    },
    theme: {
        options: {
            customProperties: true,
        },
        themes: {
            light: {
                colors: {
                    primary: '#9155FD', // основной цвет
                    secondary: '#8A8D93', // второстепенный цвет
                    accent: '#0d6efd',
                    success: '#56CA00',
                    info: '#16B1FF',
                    warning: '#FFB400',
                    error: '#FF4C51',
                },
            },
        },
    },
    components,
    directives,
});

export default vuetify;
