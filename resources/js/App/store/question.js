import api from '../utils/api'
import {defineStore} from "pinia";
import {useCurrentSkuStore} from "./currentSku";
const currentSkuStore = useCurrentSkuStore();
import {useNotificationStore} from "./notification";
const notificationStore = useNotificationStore();

export const useQuestionStore = defineStore({
    id: 'question',
    state: () => ({
        isLoadingQuestions: false,
        questionWithPagination: [],
        tableOptions: {
            currentPage: 1,
        },
        total: 0,
        lastPage: 1
    }),
    actions: {
        setTableOptions(payload) {
            this.tableOptions = { ...payload };
        },
        setTableOptionsToDefault() {
            this.tableOptions = { page: 1};
        },
        setTableOptionsByQuery(query) {
            this.tableOptions.currentPage = 1
            for (let [key, value] of Object.entries(query)) {
                if (key === 'page') {
                    this.tableOptions.currentPage = Number(value)
                }
            }
        },
        async reloadQuestions() {
            this.setTableOptionsToDefault();
            await this.loadQuestionsWithPagination();
        },
        async loadQuestionsWithPagination() {
            const skuId = currentSkuStore.currentSkuId;
            if (skuId) {
                this.isLoadingQuestions = true;

                const params = {
                    page: this.tableOptions.currentPage,
                    sku_id: skuId
                }

                const { data } = await api.get('questions', { params });
                if (data) {
                    this.questionWithPagination = [...data.data];
                    this.total = data.total;
                    this.lastPage = data.last_page;
                }
                this.isLoadingQuestions = false;
            }
        },
        async createQuestion(obj) {
            const skuId = currentSkuStore.currentSkuId;
            if (skuId) {
                obj.sku_id = skuId;
                const { data } = await api.post('questions', obj);
                notificationStore.setSuccess('Вопрос успешно создан и будет опубликован после модерации');
                await this.reloadQuestions();
            }
        }
    },
});
