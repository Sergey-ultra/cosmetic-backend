import api from '../utils/api'
import {defineStore} from "pinia";
import {useNotificationStore} from "./notification";
const notificationStore = useNotificationStore();

export const useSubscriptionStore = defineStore({
    id: 'subscription',
    actions: {
         async create(obj) {
            const res = await api.post('/subscription', obj)
             if (res) {
                 notificationStore.setSuccess(res.message);
             }
        }
    }
});
