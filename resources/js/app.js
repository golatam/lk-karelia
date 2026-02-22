import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';

createInertiaApp({
    title: (title) => title ? `${title} — Личный кабинет ИБ РК` : 'Личный кабинет ИБ РК',
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.directive('click-outside', {
            mounted(el, binding) {
                el._clickOutside = (event) => {
                    if (!el.contains(event.target)) {
                        binding.value(event);
                    }
                };
                document.addEventListener('click', el._clickOutside);
            },
            unmounted(el) {
                document.removeEventListener('click', el._clickOutside);
            },
        });

        app.use(plugin).mount(el);
    },
});
