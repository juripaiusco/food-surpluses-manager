import './bootstrap';
import '../css/app.css';
import '../scss/style.scss';
import '../../node_modules/bootstrap/dist/js/bootstrap.bundle';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import { plugin as formkitPlugin, defaultConfig } from '@formkit/vue'
import { createAutoHeightTextareaPlugin } from '@formkit/addons'

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(formkitPlugin, defaultConfig({
                plugins: [createAutoHeightTextareaPlugin()],
                locales: ['it'],
                locale: 'it',
                messages: {
                    it: {
                        validation: {
                            required: ({ name }) => `Il campo ${name} è obbligatorio.`,
                        },
                    },
                },
            }))
            .mount(el);
    },
    progress: {
        showSpinner: true,
        delay: 400,
        color: '#0ea5e9',
    },
});
