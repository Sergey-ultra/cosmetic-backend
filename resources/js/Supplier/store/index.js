import { createStore } from 'vuex'
import auth from './auth'
import notification from './notification'
import store from './store'

export default  createStore({
    modules: {
        auth,
        notification,
        store
    }
})

