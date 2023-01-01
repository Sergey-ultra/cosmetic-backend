import { createApp } from 'vue/dist/vue.esm-bundler.js';
import store from "./store";
import router from './router/index'
import CKEditor from '@ckeditor/ckeditor5-vue';
import { library } from '@fortawesome/fontawesome-svg-core'
import { fas } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import Admin from './Admin.vue'



library.add(fas)

createApp(Admin)
    .component('fa', FontAwesomeIcon)
    .use(store)
    .use(router)
    .use( CKEditor )
    .mount('#admin')


