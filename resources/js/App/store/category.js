import api from '../utils/api'
import {defineStore} from "pinia";

export const useCategoryStore = defineStore({
    id: 'category',
    state: () => ({
        isLoading: false,
        currentCategory: {},
        categories: [],
        isLoadingCategories: false
    }),
    actions: {
        async loadNestedCategories() {
            this.isLoadingCategories = true;
            const { data } = await api.get(`/categories/nested`);
            if (data) {
                this.categories = [...data];
            }
            this.isLoadingCategories = false;
        },
        async loadCurrentCategory(categoryCode) {
            this.isLoading = true;
            const { data } = await api.get(`/categories/${categoryCode}`);
            if (data) {
                this.currentCategory = { ...data };
            }
            this.isLoading = false;
        },
    },
});
