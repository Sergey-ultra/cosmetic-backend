import api from '../utils/api';
import {defineStore} from "pinia";

export const useComparisonStore = defineStore({
    id: 'comparison',
    state: () => ({
        compared:[],
        comparedSkus:[],
        isLoadingComparedSkus: false,
        currentComparedSku: null,
        currentCategoryId: null
    }),
    getters: {
       categoryIdWithMaxSkuIds: state => {
           let max = 0
           let categoryId
           state.compared.forEach(el => {
               if (el.ids.length > max) {
                   max = el.ids.length
                   categoryId = el.category_id
               }
           })
           return categoryId
       },
        categoryIdsWithComparedSkuIds: state => state.compared.map(el => el.category_id),
        allComparedSkuIdsCount: state => state.compared.reduce((accumulator, currentValue) => accumulator + currentValue.ids.length, 0),
        allComparedSkuIds: state => state.compared.reduce((accumulator, currentValue) => {
            return accumulator.concat(currentValue.ids)
        }, [])
    },
    actions: {
        setCurrentCategoryId(id) {
            this.currentCategoryId = id;
        },
        setCurrentComparedSkuToNull() {
            this.currentComparedSku = null;
        },
        setComparedSkus(payload) {
            this.comparedSkus = [...payload];
        },
        addNewCategoryToComparedIds(comparedSku)  {
            this.compared.push({
                category_id: comparedSku.category_id,
                name: comparedSku.category,
                ids: [ comparedSku.id ]
            })
        },
        removeFromComparedIds(comparedSku) {
            let categoryId;
            if (comparedSku.category_id) {
                categoryId = comparedSku.category_id;
            } else {
                categoryId = this.currentCategoryId;
            }

            const existingCategoryWithSkuIds = this.compared.find(el => el.category_id === categoryId);
            const index = existingCategoryWithSkuIds.ids.indexOf(comparedSku.id);
            existingCategoryWithSkuIds.ids.splice(index, 1);
        },
        async checkCompared() {
            let array = await JSON.parse(localStorage.getItem('comparedSkuIds'));
            if (array) {
                this.compared = [...array];
            }
        },
        async toggleComparison(object) {
            this.setCurrentComparedSkuToNull();
            let currentComparedSku = {...object};

            const existingCategoryWithSkuIds = this.compared.find(el => el.category_id === currentComparedSku.category_id);
            if (!existingCategoryWithSkuIds) {
                this.compared.push({
                    category_id: currentComparedSku.category_id,
                    name: currentComparedSku.category,
                    ids: [ currentComparedSku.id ]
                });

                this.setCurrentCategoryId(currentComparedSku.category_id);
                currentComparedSku.status = 'add';
            } else {
                if (!existingCategoryWithSkuIds.ids.includes(currentComparedSku.id)) {
                    const existingCategoryWithSkuIds = this.compared.find(el => el.category_id === currentComparedSku.category_id);
                    existingCategoryWithSkuIds.ids.push(currentComparedSku.id);

                    this.setCurrentCategoryId(currentComparedSku.category_id);
                    currentComparedSku.status = 'add';
                } else {
                   this.removeFromComparedIds(currentComparedSku);
                    currentComparedSku.status = 'remove';
                }
            }

            setTimeout(() => {
                this.currentComparedSku = { ...currentComparedSku };
            }, 200);

            await localStorage.setItem('comparedSkuIds', JSON.stringify([...this.compared]))
        },
        async removeFromComparedSku(sku) {
            const index = this.comparedSkus.findIndex(el => el.id === sku.id);
            this.comparedSkus.splice(index, 1);

            this.removeFromComparedIds(sku);
            await localStorage.setItem('comparedSkuIds', JSON.stringify([...this.compared]));
        },
        async removeIdsInCurrentCategory() {
            const index = this.compared.findIndex(el => el.category_id === this.currentCategoryId);
            this.compared.splice(index, 1);
            this.setComparedSkus([]);
            this.setCurrentCategoryId(this.categoryIdWithMaxSkuIds);
            await localStorage.setItem('comparedSkuIds', JSON.stringify([...this.compared]));
        },
        async loadComparedSkus() {
            this.isLoadingComparedSkus = true;

            if (this.compared.length) {
                if (!this.currentCategoryId) {
                    this.setCurrentCategoryId(this.categoryIdWithMaxSkuIds);
                }

                const ids = this.compared.find(el => el.category_id === this.currentCategoryId).ids;

                const { data } = await api.get('show-compared-skus', { params: { ids }});
                if (data) {
                    this.setComparedSkus(data);
                }
            } else {
                this.setComparedSkus([]);
            }
            this.isLoadingComparedSkus = false;
        },
    },
});
