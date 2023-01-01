import api from '../utils/api';
import {defineStore} from "pinia";
import {useCurrentSkuStore} from "./currentSku";
const currentSkuStore = useCurrentSkuStore();
import {useNotificationStore} from "./notification";
const notificationStore = useNotificationStore();

export const useReviewStore = defineStore({
    id: 'review',
    state: () => ({
        selectedRating: 0,
        isLoadingReviews:false,
        reviewsWithPagination:[],
        tableOptions: {
            currentPage: 1,
            rating: []
        },
        total: 0,
        lastPage: 1,
        reviewImages:[],
        ratingFilter: {},
        isLoadingReviewImages: false,
        existingReview: null,
        isCheckingExistingReview: false,
        myReviews:[],
        isLoadingMyReviews: false,
    }),
    getters: {
        reviewsByRating: state => {
            let reviewsByRating = {};

            [5, 4, 3, 2, 1].forEach(rating => {
                const currentRating = state.reviewsWithPagination.filter(el => Number(el.rating) === rating);
                if (currentRating.length) {
                    reviewsByRating[rating] = { count: currentRating.length };
                }
            })
          return reviewsByRating
        }
    },
    actions: {
        setSelectedRating(payload) {
            this.selectedRating = payload;
        },
        setExistingReview(payload) {
            this.existingReview = payload;
        },
        setTableOptionsByQuery(query) {
            this.tableOptions.currentPage = 1;
            for (let [key, value] of Object.entries(query)) {
                if (key === 'page') {
                    this.tableOptions.currentPage = Number(value);
                } else if (key === 'rating' && value) {
                    this.tableOptions.rating = [...value];
                }
            }
        },
        async checkUserRating() {
            const skuId = currentSkuStore.currentSkuId;
            if (skuId) {
                const { data } = await api.post('/rating/check-user-rating', { sku_id: skuId});

                if (data.status === 'success') {
                    this.setSelectedRating(data.data);
                }
            }
        },
        async createOrUpdateRating(rating) {
            const skuId = currentSkuStore.currentSkuId;
            if (skuId) {
                const { data } = await api.post('/rating/create-or-update', {
                    sku_id: skuId,
                    rating
                })
                if (data.status === 'success') {
                    this.setSelectedRating(rating)
                }
            }
        },
        async loadReviewsWithPagination() {
            const skuId = currentSkuStore.currentSkuId;

            if (skuId) {
                this.isLoadingReviews = true;
                const params = { page: this.tableOptions.currentPage };
                if (this.tableOptions.rating.length) {
                    params['filter[rating]'] = this.tableOptions.rating;
                }

                const  data  = await api.get(`/reviews/by-sku-id/${skuId}`, { params });
                if (data) {
                    this.reviewsWithPagination = [...data.data];
                    this.total = data.meta.total;
                    this.lastPage = data.meta.last_page;
                }
                this.isLoadingReviews = false;
            }
        },
        async loadReviewAdditionalInfo() {
            const skuId = currentSkuStore.currentSkuId;

            if (skuId) {
               this.isLoadingReviewImages = true;

                const { data }  = await api.get(`/reviews/additional-info-by-sku-id/${skuId}`)
                if (data) {
                    this.reviewImages = [...data.images];
                    this.ratingFilter = {...data.rating_filter};
                }
                this.isLoadingReviewImages = false;
            }
        },
        async loadMyRatingsWithReviews() {
            this.isLoadingMyReviews = true;
            const { data } = await api.get('/reviews/my');
            if (data) {
                this.myReviews = [...data];
            }
            this.isLoadingMyReviews = false;
        },
        async checkExistingReview() {
            const skuId = currentSkuStore.currentSkuId;
            if (skuId) {
                this.isCheckingExistingReview = true;

                const { data } = await api.post('/review/check-existing-review', { sku_id: skuId });

                if (data) {
                    this.setExistingReview(data);
                    this.setSelectedRating(Number(data.rating));
                } else {
                    this.setExistingReview(null);
                }
                this.isCheckingExistingReview = false;
            }
        },
        async updateOrCreateReview(obj) {
            const skuId = currentSkuStore.currentSkuId;
            if (skuId) {
                obj.sku_id = skuId;

                const { data } = await api.post('/reviews', obj);

                if (data.status === 'success') {
                    this.setExistingReview(data.data);
                    notificationStore.setSuccess('Отзыв успешно создан и будет опубликован после модерации');
                }
            }
        },
        async deleteReview(id) {
            await api.delete(`/reviews/${id}`)
            await this.loadMyRatingsWithReviews();
            notificationStore.setSuccess('Отзыв успешно удален');
        }
    }
});
