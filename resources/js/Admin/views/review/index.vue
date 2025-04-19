<template>
    <data-table
            :items="reviews"
            :headers="headers"
            :isLoading="isLoading"
            :optionsItemsPerPage="optionsItemsPerPage"
            :total="total"
            :availableOptions="availableOptions"
            v-model:options="options"
            v-model:filter="filter"
            @reloadTable="reloadReviews"
    >

        <template v-slot:add>
            <buttonComponent :size="'small'" :color="'blue'" @click="showForm(null)">Добавить</buttonComponent>
        </template>

        <template v-slot:product_name="review">
            <a :href="review.item.product_link" :class="{'moderated': review.item.sku_status === 'moderated'}" class="flex">
                <img class="image" :src="review.item.sku_image">
                <span>{{ review.item.product_name }}</span>
            </a>
        </template>
        <template v-slot:anonymously="review">
            {{ review.item.anonymously === 1 ? 'Да' : '' }}
        </template>

        <template v-slot:images="review">
            <div class="image__wrapper">
                <img v-for="image in review.item.images" :key="image" class="image" :src="image" :alt="image">
            </div>
        </template>

        <template v-slot:action="review">
            <div class="action">
                <drop-menu :items="getActionsOptions(review.item.review_id)">
                    <template v-slot:publish
                              v-if="review.item.review_status !== 'published'"
                    >
                        <div class="dropdown__inner">
                            <button
                                    class="dropdown__value"
                                    @click="setReviewStatus({ id: review.item.id, status: 'published' })"
                            >
                                <span>Опубликовать</span>
                            </button>
                        </div>
                    </template>
                    <template v-slot:moderated v-if="review.item.review_status !== 'moderated'">
                        <div class="dropdown__inner">
                            <button
                                class="dropdown__value"
                                @click="setReviewStatus({ id: review.item.id, status: 'moderated' })"
                            >
                                <span>Поставить на модерацию</span>
                            </button>
                        </div>
                    </template>
                    <template v-slot:rejected  v-if="review.item.review_status !== 'rejected'">
                        <div class="dropdown__inner">
                            <button
                                class="dropdown__value"
                                @click="showRejectModal(review.item.id)"
                            >
                                <span>Отклонить</span>
                            </button>
                        </div>
                    </template>
                    <template v-slot:deleted v-if="review.item.rating_status !== 'deleted' && review.item.review_status !== 'deleted'">
                        <div class="dropdown__inner">
                            <button
                                class="dropdown__value"
                                @click="setReviewStatus({ id: review.item.id, status: 'deleted' })"
                            >
                                <span>Удалить</span>
                            </button>
                        </div>
                    </template>
                </drop-menu>
            </div>
            <div class="action" @click="showForm(review.item.review_id)">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     class="bi bi-pencil-fill" viewBox="0 0 16 16">
                    <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                </svg>
            </div>
        </template>
    </data-table>

    <review-form
        :selectedReviewId="selectedReviewId"
        v-model:isShowForm="isShowForm"
        v-if="isShowForm"/>

    <rejectedForm
        v-model:isShowRejectModal="isShowRejectModal"
        v-if="isShowRejectModal"
        :rejectedReviewId="rejectedReviewId"
    />
</template>

<script>
    import dataTable from '../../components/data-table.vue'
    import modal from '../../components/modal/modal.vue'
    import reviewForm from './review-form.vue'
    import dropMenu from '../../components/drop-menu.vue'
    import {mapActions, mapMutations, mapState} from "vuex";
    import buttonComponent from "../../components/button-component.vue";
    import rejectedForm from "./rejected-form.vue";

    export default {
        name: "review",
        components:{
            dataTable,
            modal,
            buttonComponent,
            reviewForm,
            dropMenu,
            rejectedForm,
        },
        data: () => ({
            headers: [
                {title: 'id', value: 'id', width: '3%'},
                {title: 'Имя sku', value: 'product_name', width: '20%'},
                {title: 'Юзер', value: 'user', width: '15%', filter: { type: 'input' }},
                {title: 'Рейтинг', value: 'rating', width: '5%', filter: { type:'select' }},
                {title: 'Название отзыва', value: 'title', width: '20%', filter: { type:'input' }},
                {title: 'Просмотры', value: 'views_count', width: '5%'},
                {title: 'Лайки', value: 'likes_count', width: '5%'},
                {title: 'Статус отзыва', value: 'review_status', width: '15%', filter: { type: 'select' }},
                {title: 'Действия', value: 'action', width: '5%'}
            ],
            optionsItemsPerPage: [5, 10, 20, 50, 100],
            isShowForm: false,
            selectedReviewId: null,
            selectedName: null,
            isShowRejectModal: false,
            rejectedReviewId: null,
        }),
        computed:{
            ...mapState('review', ['tableOptions', 'filterOptions', 'isLoading', 'reviews', 'total']),
            availableOptions() {
                return {
                    review_status: ['moderated', 'published', 'rejected', 'deleted'].map(el => ({ title: el, value: el })),
                    rating : [1, 2, 3, 4, 5].map(el => ({ title: el, value: el })),
                }
            },
            filter: {
                get() {
                    return this.filterOptions
                },
                set(value) {
                    this.setFilterOptions(value)
                    this.loadReviews()
                }
            },
            options: {
                get() {
                    return this.tableOptions
                },
                set(value) {
                    this.setTableOptions(value)
                    this.loadReviews()
                }
            }
        },
        created() {
            if (!this.reviews.length) {
                this.loadReviews()
            }
        },
        watch: {

        },
        methods: {
            ...mapActions('review', ['reloadReviews', 'loadReviews', 'setReviewStatus', 'reject']),
            ...mapMutations('review', ['setTableOptions', 'setFilterOptions']),
            getActionsOptions(reviewId) {
                if (reviewId !== null) {
                    return ['publish', 'moderated', 'rejected', 'deleted']
                }
                return ['publish','deleted']
            },
            showForm(id) {
                this.isShowForm = true
                this.selectedReviewId = id
            },
            showRejectModal(reviewId) {
                this.isShowRejectModal = true;
                this.rejectedReviewId = reviewId;
            },
        }
    }
</script>

<style scoped lang="scss">
    @import './resources/css/admin/table.scss';
    .flex {
        display:flex;
    }
    .moderated {
        color: red;
    }
</style>
