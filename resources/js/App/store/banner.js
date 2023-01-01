import api from '../utils/api'
import {defineStore} from "pinia";

export const useBannerStore = defineStore({
    id: 'banner',
    state: () => ({
        isShowBanner: true,
        bannerUrls: [],
        isLoadingBanner: false
    }),
    actions: {
        setShowBanner(payload) {
            this.isShowBanner = payload;
        },
        async loadBanner() {
            this.isLoadingBanner = true;
            const { data } = await api.get(`banner`);
            if (data) {
                this.bannerUrls = [...data];
            }
            this.isLoadingBanner = false;
        }
    }
});
