import api from '../utils/api'
import {defineStore} from "pinia";

export const useViewedSkuStore = defineStore({
    id: 'viewedSku',
    state: () => ({
        viewedSkuId: [],
        viewedSkus: [],
        isLoadingViewedSkus: false
    }),
    actions: {
        addToViewedSkuIds(id) {
            if (!this.viewedSkuId.includes(id)) {
                if (this.viewedSkuId.length >= 20) {
                    this.viewedSkuId.shift();
                }
                this.viewedSkuId.push(id);
            }
        },
        deleteFromViewedSkuIds(id) {
            const findedIndex = this.viewedSkuId.indexOf(id);
            if (findedIndex !== -1) {
                this.viewedSkuId.splice(findedIndex, 1);
            }
        },
        async checkViewedSkuIds() {
            let ids = await JSON.parse(localStorage.getItem('viewedProducts'));
            if (ids && Array.isArray(ids)) {
                ids.forEach(id => this.addToViewedSkuIds(id));
            }
        },
        async loadViewedSkus() {
            this.isLoadingViewedSkus = true;
            if (this.viewedSkuId.length) {
                localStorage.setItem('viewedProducts', JSON.stringify([...this.viewedSkuId]));

                const { data } = await api.get('/skus/viewed', { params: { ids: this.viewedSkuId } });
                if (data && Array.isArray(data)) {
                    this.viewedSkus = [...data];
                }
            }
            this.isLoadingViewedSkus = false;
        }
    }
});
