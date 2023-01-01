import api from '../utils/api'
import {defineStore} from "pinia";

export const useTrackingStore = defineStore({
    id: 'tracking',
    state: () => ({
        trackingSkuIds:[],
        isSuccessToAddingToTrackingSkuIds: false
    }),
    actions: {
        async checkTrackingSkuIds() {
            let ids = await JSON.parse(localStorage.getItem('trackingSkuIds'));
            if (ids && Array.isArray(ids)) {
                this.trackingSkuIds = [...ids];
            }
        },
        async addToTracking(obj) {
            if (!this.trackingSkuIds.includes(obj.sku_id)) {
                this.trackingSkuIds.push(obj.sku_id);

                localStorage.setItem('trackingSkuIds', JSON.stringify([...this.trackingSkuIds]));
                const { data } = await api.post('add-to-tracking', obj);
                if (data && data.status === 'success') {
                    this.isSuccessToAddingToTrackingSkuIds = true;
                }
            }
        },
    }
});
