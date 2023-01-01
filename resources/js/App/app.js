require('../bootstrap');

import { createApp } from 'vue/dist/vue.esm-bundler.js';
import pinia from './store';
import router from './router'
import { library } from '@fortawesome/fontawesome-svg-core'
import { fas } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import App from './App.vue'
import lazyload from './directives/lazyload'


//import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');
//
// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });

library.add(fas)



const app = createApp(App);
app.use(pinia);
app.use(router);
app.directive("lazyload", lazyload);
app.component('fa', FontAwesomeIcon);
app.mount('#app');


