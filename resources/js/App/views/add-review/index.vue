<template>
    <compact-sku>
        <div class="review">
            <h2 class="">Отзыв о товаре</h2>
            <div
                    v-if="isCheckingExistingReview"
                    class="loader-wrapper"
            >
                <loader class="loader"/>
            </div>
            <form v-else class="review__form" @submit.prevent="saveReview" >
                <div class="form-group">
                    <h4>Оценка модели</h4>

                    <div>
                        <ratingForm :initLoading="false"/>
                        <div class="invalid-feedback" v-for="error of v$.rating.$errors" :key="error.$uid">
                            {{ error.$message }}
                        </div>
                    </div>
                </div>
                <div class="form-group ">
                    <textarea rows="5" v-model.trim="editedReview.comment" placeholder="Комментарий"></textarea>
                    <div class="invalid-feedback" v-for="error of v$.editedReview.comment.$errors" :key="error.$uid">
                        {{ error.$message }}
                    </div>
                </div>
                <div class="form-group ">
                    <textarea rows="5" v-model.trim="editedReview.plus" placeholder="Достоинства"></textarea>
                    <div class="invalid-feedback" v-for="error of v$.editedReview.plus.$errors" :key="error.$uid">
                        {{ error.$message }}
                    </div>
                </div>
                <div class="form-group ">
                    <textarea rows="5" v-model.trim="editedReview.minus" placeholder="Недостатки"></textarea>
                    <div class="invalid-feedback" v-for="error of v$.editedReview.minus.$errors" :key="error.$uid">
                        {{ error.$message }}
                    </div>
                </div>


                <h4>Фотографии отзыва</h4>

                <multiple-image-upload
                        :folder="'premoderatedReviews'"
                        v-model:initialImageUrls="editedReview.images"
                        :photoCountInRow="photoCountInRow"
                        :height="150"

                />
                <div class="form-group ">
                    <label>
                        <input v-model="anonymouslyLocal" type="checkbox"/>
                        <span>Оставить отзыв анонимно</span>
                    </label>
                </div>


                <div class="form-group mt-4">
                    <button class="add-btn" :disabled="isUploadingReview" type="submit">Отправить</button>

                    <div class="agreement">
                        Нажимая кнопку «Отправить», Вы соглашаетесь c
                        <span>
                                <router-link :to="{ name: 'policy' }" target="_blank">
                                    условиями политики конфиденциальности.
                                </router-link>
                            </span>
                    </div>
                </div>
            </form>
        </div>
    </compact-sku>
</template>

<script setup>
    import multipleImageUpload from "../../components/image-upload-as-form/multiple-image-upload";
    import ratingForm from "../../components/ratingForm";
    import compactSku from '../../components/compact-sku'
    import loader from "../../components/loader";
    import useVuelidate from '@vuelidate/core'
    import { required, minLength, helpers } from '@vuelidate/validators';
    import { storeToRefs } from "pinia";
    import {useReviewStore} from "../../store/review";
    import {useAuthStore} from "../../store/auth";
    import {useCurrentSkuStore} from "../../store/currentSku";
    import {computed, onMounted, reactive, ref, watch} from "vue";
    import {useRouter} from "vue-router";

    const mustBeRating = value => value > 0;

    const reviewStore = useReviewStore();
    const authStore = useAuthStore();
    const currentSkuStore = useCurrentSkuStore();

    const { isUploadingReview, existingReview, selectedRating, isCheckingExistingReview } = storeToRefs(reviewStore);
    const { isAuth } = storeToRefs(authStore);
    const { currentSkuId } = storeToRefs(currentSkuStore);

    let rating = ref(0);

    let editedReview = ref({
        comment:'',
        plus:'',
        minus:'',
        images:[],
        anonymously: 0
    });

    let rules = {
        rating: {
            mustBeRating:  helpers.withMessage('Нужно оценить товар', mustBeRating)
        },
        editedReview: {
            comment: {
                required:  helpers.withMessage('Поле должно быть заполнено', required),
                minLength: minLength(2)
            },
            plus: { required:  helpers.withMessage('Поле должно быть заполнено', required) },
            minus: { required:  helpers.withMessage('Поле должно быть заполнено', required) },
        }
    };
    const v$ = useVuelidate(rules, {editedReview, rating});

    const photoCountInRow = computed(() => {
        const width = document.documentElement.clientWidth
        if (width < 500) {
            return 1;
        }
        if (width > 500 && width < 900) {
            return 2;
        }
        return 4;
    });

    const anonymouslyLocal = computed({
        get() {
            return Boolean(editedReview.value.anonymously);
        },
        set(value) {
            editedReview.value.anonymously = Number(value);
        }
    });

    watch(
        existingReview,
        value => {
            if (value) {
                initEditedReview();
            }
        }
    );

    watch(selectedRating, value => rating.value = value);

    watch(currentSkuId, async (value) => {
            if (value && isAuth) {
                await reviewStore.checkExistingReview();
            }
        }
    );

    const initEditedReview = () => {
        editedReview.value = {
            ...existingReview.value,
            images: Array.isArray(existingReview.value.images)
                ? [...existingReview.value.images]
                : []
        }
    };

    onMounted(async () => {
        rating.value = selectedRating.value;
        if (isAuth && currentSkuId) {
            await reviewStore.checkExistingReview();
        }
    });

    let router =  useRouter();

    const saveReview = async () => {
        if (!isAuth.value) {
            authStore.setIsShowAuthModal(true);
        } else {
            const validated = await v$.value.$validate();

            if (validated) {
                await reviewStore.updateOrCreateReview(editedReview.value);
                editedReview.value = {
                    comment: '',
                    plus: '',
                    minus: '',
                    images: []
                };
                rating.value = 0;
                v$.value.$reset();
                router.push({ name: 'description' });
            }
        }
    }
</script>

<style lang="scss" scoped>
    .loader {
        width: 70px;
        height: 70px;

        &-wrapper {
            display: flex;
            justify-content: center;
            position: absolute;
            z-index: 3;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.82);
        }
    }



    input[type=checkbox] {
        margin-right: 10px;
    }

    textarea {
        width: 100%;
        resize: vertical;
        border-radius: 8px;
        border: 1px solid #d9d9d9;
        padding: 0 10px;
        color: #333;
        outline: none;
    }
    .review {
        position: relative;
        &__form {
            width: 100%;
        }

    }

    .rating {
        display: flex;
        &__item {
            height: 36px;
            width: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 0;

            & svg path {
                fill: #d9d9d9;
            }
            &-selected svg path {
                fill: #ffda66;
            }
        }

    }

    .form-label {
        color:#42b983;
        font-size:20px;
    }
    .form-check {
        margin-right: 25px;
    }

    .form-group {
        margin: 12px 0;
        & textarea {
            min-height: 100px;
        }
    }
    .add-btn {
        margin-top: 50px;
        min-width: 230px;
        border-radius: 4px;
        transition: all 0.12s ease-out;
        background-color: #fc0;
        padding: 0 20px;
        width: 25%;
        line-height: 48px;
        height: 48px;
        color: inherit;
        text-decoration: none;
        text-align: center;
        border:0;
        &:hover {
            background-color: #f5c423;
        }
    }
    .agreement {
        font-size:14px;
        margin-top: 20px;
    }
    .mt-4 {
        margin-top:20px;
    }

    .invalid-feedback {
        color: #fc675c;
    }
</style>
