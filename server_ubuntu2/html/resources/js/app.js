import './bootstrap';
// import Alpine from 'alpinejs';

// window.Alpine = Alpine;

// Alpine.start();

import Echo from "laravel-echo"

window.Echo = new Echo({
    broadcaster: 'socket.io',
    host: window.location.hostname + ':6001'
});


import { createApp } from 'vue'
import ChatLayout from '../components/ChatLayout.vue'

const app = createApp(ChatLayout)

app.mount('#app')




