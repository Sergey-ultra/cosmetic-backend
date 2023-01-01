import api from '../utils/api'
import {defineStore} from "pinia";

export const useCurrentSkuStore = defineStore({
    id: 'currentSku',
    state: () => ({
        currentSkuId: null,
        isLoadingCurrentSku: false,
        currentSku:{},
        compactCurrentSku: {},
        isLoadingCompactCurrentSku: false,
    }),
    actions: {
        setCurrentSkuId(payload) {
            this.currentSkuId = payload;
        },
        async loadCurrentSku() {
            this.isLoadingCurrentSku = true;
            const { data } = await api.get(`/skus/by-sku-id/${this.currentSkuId}`);
            if (data) {
                this.currentSku = {...data};
            }
            this.isLoadingCurrentSku = false;
        },
        async loadCompactCurrentSku() {
            if (this.currentSkuId) {
                this.isLoadingCompactCurrentSku = true;

                const {data} = await api.get(`/skus/by-sku-id/${this.currentSkuId}`, {params: {view: 'compact'}})
                if (data) {
                    this.compactCurrentSku = {...data};
                }
                this.isLoadingCompactCurrentSku = false;
            }
        },
    }
});
