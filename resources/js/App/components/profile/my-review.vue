<template>
    <my-published
            @showEditForm="showEditForm"
            @showDeleteForm="showDeleteForm"
    >
        <template v-slot:status v-if="review.status && ['moderated', 'rejected'].includes(review.status)">
            <div
                    v-if="review.status === 'moderated'"
                    class="status status-moderated"
            >
                <span class="status__icon">
                    <svg><use xlink:href="#icons_question-circle">
                        <symbol viewBox="0 0 24 24" id="icons_question-circle">
                            <path fill-rule="evenodd" d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11 11-4.925 11-11S18.075 1 12 1m0 1.75c5.1 0 9.25 4.15 9.25 9.25S17.1 21.25 12 21.25 2.75 17.1 2.75 12 6.9 2.75 12 2.75zm-1.5 13.293c0 .718.479 1.162 1.244 1.162.766 0 1.237-.444 1.237-1.162 0-.725-.471-1.169-1.237-1.169-.765 0-1.244.444-1.244 1.169zm1.456-8.996c-2.099 0-3.288 1.176-3.322 2.878h1.852c.041-.752.561-1.237 1.347-1.237.779 0 1.299.451 1.299 1.086 0 .636-.267.964-1.149 1.491-.943.553-1.319 1.169-1.23 2.262l.014.376h1.811v-.362c0-.656.253-.991 1.162-1.518.964-.567 1.463-1.285 1.463-2.31 0-1.579-1.292-2.666-3.247-2.666z"></path>
                        </symbol>
                    </use></svg>
                </span>
                <span >Ожидает публикации</span>
            </div>
            <div
                    v-else-if="review.status === 'rejected'"
                    class="status status-rejected"
            >
                <span >Отзыв отклонен</span>
            </div>
        </template>

        <template v-slot:title>
            <router-link
                    :to="{ name: 'description', params: { product_code: productCode }}"
                    class="review__sku"
            >
                <div class="review__img">
                    <img :src="review.sku_image" :alt="review.sku_image">
                </div>
                <div class="review__sku-name">{{ review.sku_name }}, {{ review.volume }}</div>
            </router-link>
        </template>

        <div
                class="review"
                :class="{'review-filter': review.status === 'moderated'}"
        >

           <review-common
                   :review="review"
                   :isRight="true"
           />

        </div>
        <div class="review__bottom" v-if="review.status === 'rejected'">
            <h3>Причина отклонения</h3>
            <div class="review__text">Ваш отзыв не соответствует требованиям к публикациям.</div>
            <router-link
                    class="review__action"
                    :to="{ name: 'add-review', params: { product_code: productCode }}"
            >
                <span>Редактировать отзыв</span>
            </router-link>
        </div>
        <div class="review__bottom" v-else-if="!review.comment && !review.plus && !review.minus">
            <h3> Расскажите поподробнее</h3>
            <div class="review__text">Будет здорово, если вы напишете свои впечатления о товаре. Это поможет другим
                покупателям
            </div>
            <router-link
                    class="review__action"
                    :to="{ name: 'add-review', params: { product_code: productCode }}"
            >
                <span>Оставить отзыв</span>
            </router-link>
        </div>
        <div class="review__bottom" v-else-if="Array.isArray(review.images) && !review.images.length">
                <div class="review__text">
                    Будет здорово, если вы дополните отзыв фотографиями — это поможет другим покупателям узнать о товаре
                    больше.
                </div>
                <router-link
                        class="review__action"
                        :to="{ name: 'add-review', params: { product_code: productCode }}"
                >
                    <div class="review__icon">
                        <div class="review__icon-inner">
                            <svg><use xlink:href="#icons_attachment">
                                <symbol viewBox="0 0 24 24" id="icons_attachment">
                                    <path fill-rule="evenodd" d="M10.63 23.024L9.216 21.61l9.4-9.4c1.748-1.748 1.86-4.698.25-6.577l-.17-.197c-.754-.882-1.781-1.39-2.89-1.433-1.09-.04-2.17.387-2.992 1.208L4.76 13.264c-.49.49-.76 1.143-.761 1.838-.001.63.22 1.225.627 1.696a2.608 2.608 0 0 0 3.533-.143l8.27-8.27L17.844 9.8l-8.27 8.27a4.613 4.613 0 0 1-6.215.276l-.012.012-.275-.274-.006-.007h-.001l-.001-.002.012-.012A4.566 4.566 0 0 1 2 15.102a4.574 4.574 0 0 1 1.347-3.252L11.4 3.797c1.219-1.22 2.81-1.859 4.484-1.793 1.67.064 3.21.821 4.332 2.13l.17.198c2.274 2.653 2.115 6.822-.356 9.292l-9.4 9.4z"></path>
                                </symbol>
                            </use></svg>
                        </div>
                    </div>
                    <span>Добавить фото</span>
                </router-link>
            </div>

    </my-published>

    <delete-form
            :selectedName="`Отзыв на ${review.sku_name}`"
            v-if="isShowDeleteForm"
            v-model:isShowDeleteForm="isShowDeleteForm"
            @delete="deleteCurrentReview"
    />
</template>

<script setup>
    import dropMenu from '../drop-menu'
    import deleteForm from '../delete-form'
    import myPublished from "./my-published";
    import reviewCommon from '../review-common'
    import {ref, defineProps, computed} from 'vue';
    import {useReviewStore} from "../../store/review";
    import {useRouter} from "vue-router";

    const reviewStore = useReviewStore();


    const isShowDeleteForm = ref(false);
    const isShowEditForm = ref(false);
    const photoMainIndex = ref(0);

    const router = useRouter();

    const props = defineProps({
        review: {
            type: Object
        }
    });

    const productCode = computed(() =>  props.review.product_code + '-' + props.review.sku_id);

    const showDeleteForm = () => isShowDeleteForm.value = true;

    const deleteCurrentReview = () => {
        if (props.review.sku_rating_id) {
            reviewStore.deleteReview(this.props.sku_rating_id);
        }
        isShowDeleteForm.value = false;
    };

    const showEditForm = () => {
       router.push({ name: 'add-review', params: { product_code:productCode.value }})
    };
</script>

<style scoped lang="scss">
    a {
        text-decoration: none;
    }
    .status {
        margin: -16px -16px 16px;
        padding: 14px 16px 14px 20px;
        font-size: 15px;
        line-height: 18px;
        &__icon svg{
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%,-50%);
            width: 20px;
            height: 20px;
        }
        &-moderated {
            background-color: #fff8d9;
            & .status__icon {
                display: inline;
                color: #ffa300;
                margin: 8px;
                position: relative;
                & svg {
                    fill: #ffa300 !important;
                }

            }
            & span {
                display: inline;
                margin: 8px;
                color: #ffa300;
            }
        }
        &-rejected {
            background-color: #ffe1e1;
            & span {
                display: inline;
                color: #c70000;
                margin: 8px;
            }
        }
    }
    .review {
        color: #222;
        &__sku {
            display: flex;
            align-items: center;
            letter-spacing: .1px;
            min-height: 40px;
        }
        &__img {
            margin-right: 5px;
            width: 40px;
            height: 40px;
            & img {
                object-fit: cover;
                width: 100%;
                height: 100%;
            }
        }

        &-filter {
            filter: grayscale(100%);
            color: #999;
        }


        &__bottom {
            margin-top: 20px;
            border-top: 1px solid #e8e8e8;
            padding-top: 20px;
        }
        &__text {
            margin-bottom: 16px;
            word-break: break-word;
        }
        &__action {
            border: 0;
            border-radius: 4px;
            position: relative;
            width: 100%;
            line-height: 36px;
            height: 36px;
            padding: 0 16px;
            transition: all .12s ease-out;
            background-color: #fc0;
            display: inline-block;
            color: inherit;
            text-decoration: inherit;
            text-align: center;
            cursor: pointer;
            &:hover {
                background-color: #f5c423;
            }
            & span {
                text-align: center;
                vertical-align: middle;
                line-height: 20px;
            }
        }
        &__icon {
            vertical-align: middle;
            display: inline-block;
            width: 32px;
            height: 32px;
            margin-left: -12px;
        }
        &__icon-inner {
            display: inline-block;
            position: relative;
            width: 32px;
            height: 32px;
            & svg {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%,-50%);
                width: 20px;
                height: 20px;
            }
        }
    }
</style>
