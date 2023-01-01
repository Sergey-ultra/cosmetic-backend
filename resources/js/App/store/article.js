import api from '../utils/api'
import { defineStore } from 'pinia'


export const useArticleStore = defineStore({
    id: 'article',
    state: () => ({
        isLoadingArticles: false,
        articles:[],
        isLoadingCurrentArticle: false,
        currentArticle: null
    }),
    actions: {
        async loadArticles() {
            this.isLoadingArticles = true;
            const { data } = await api.get('/articles/main');
            if (data !== null) {
                this.articles = [...data];
            }
            this.isLoadingArticles = false;
        },
        async loadCurrentArticle(id) {
            this.isLoadingCurrentArticle = true;

            const { data } = await api.get(`/articles/main/${id}`);
            if (data) {
                this.currentArticle = {...data };
            }
            this.isLoadingCurrentArticle = false;
        }
    }
})

