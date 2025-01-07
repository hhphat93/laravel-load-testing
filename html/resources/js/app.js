import './bootstrap';
// import Alpine from 'alpinejs';

// window.Alpine = Alpine;

// Alpine.start();

// import Echo from "laravel-echo"

// window.Echo = new Echo({
//     broadcaster: 'socket.io',
//     host: window.location.hostname + ':6001'
// });


// import ChatLayout from '../components/ChatLayout.vue'

// const app = createApp(ChatLayout)

// app.mount('#app')

import { createApp, createSSRApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'

createInertiaApp({
    resolve: name => import(`./Pages/${name}.vue`),
    setup({ el, App, props, plugin }) {
        createSSRApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
});


