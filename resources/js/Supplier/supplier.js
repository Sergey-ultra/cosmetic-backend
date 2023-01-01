import { createApp } from 'vue/dist/vue.esm-bundler.js';
import store from "./store";
import router from './router/index'
import Supplier from './Supplier.vue'


createApp(Supplier)
    .use(store)
    .use(router)
    .mount('#supplier')
