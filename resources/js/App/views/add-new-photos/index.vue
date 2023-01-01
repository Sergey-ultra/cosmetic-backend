<template>
    <compact-sku>
        <h2>Новые фотографии о товаре</h2>
        <form @submit.prevent="addReviewPhotos">
            <div class="form-group">
                <h4>Оценка модели</h4>

                <div>
                    <ratingForm/>
                    <div class="invalid-feedback" v-for="error of v$.rating.$errors" :key="error.$uid">
                        {{ error.$message }}
                    </div>
                </div>
            </div>

            <div class="form-group ">
                <textarea v-model.trim="editedReview.comment" placeholder="Комментарий" rows="6"></textarea>
                <div class="invalid-feedback" v-for="error of v$.editedReview.comment.$errors" :key="error.$uid">
                    {{ error.$message }}
                </div>
            </div>


            <multiple-images-upload
                    :folder="'premoderatedReviews'"
                    v-model:initialImageUrls="editedReview.images"
                    :photoCountInRow="photoCountInRow"
                    :height="150"

            />
            <div class="form-group mt-4">
                <button
                        class="add-btn"
                        :disabled=" editedReview.comment === '' || !editedReview.images.length"
                        type="submit"
                >
                    Отправить
                </button>

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
    </compact-sku>
</template>

<script setup>
    import multipleImagesUpload from "../../components/image-upload-as-form/multiple-image-upload";
    import compactSku from '../../components/compact-sku'
    import ratingForm from "../../components/ratingForm";
    import useVuelidate from '@vuelidate/core'
    import { required, minLength, helpers } from '@vuelidate/validators'
    import { ref, reactive, watch, computed, onMounted } from 'vue';
    import { useRouter } from 'vue-router'
    import { storeToRefs } from "pinia";
    import {useReviewStore} from "../../store/review";

    const reviewStore = useReviewStore();
    const { selectedRating, existingReview } = storeToRefs(reviewStore);

    const mustBeRating = value => value > 0;

    let rating = ref(0)
    let editedReview = reactive({
        comment:'',
        images:[]
    })

    let rules = {
        rating: {
            mustBeRating:  helpers.withMessage('Нужно оценить товар', mustBeRating)
        },
        editedReview: {
            comment: {
                required:  helpers.withMessage('Поле должно быть заполнено', required),
                minLength: minLength(2)
            },
        },
    }

    const v$ = useVuelidate(rules, {editedReview, rating});


    const photoCountInRow = computed(() => {
        const width = document.documentElement.clientWidth
        if (width < 500) {
            return 1
        }
        if (width > 500 && width < 900) {
            return 2
        }
        return 4
    });

    watch(() => selectedRating, value => rating.value = value);

    onMounted(async () => {
        rating.value = selectedRating
        if (existingReview === null) {
            await reviewStore.checkExistingReview()
        }
    });

    let router =  useRouter();
    const addReviewPhotos = async () => {
        if (!existingReview) {
            const validated = await v$.$validate()
            if (validated) {
                await reviewStore.updateOrCreateReview(editedReview)
                editedReview.value = {
                    comment: '',
                    images: []
                }
                rating.value = 0;
                v$.$reset();
                router.push({ name: 'reviews'})
            }
        }
    }
</script>

<style scoped lang="scss">
    .form-group {
        margin: 12px 0;
    }
    textarea {
        width: 100%;
        resize: vertical;
        border-radius: 8px;
        border: 1px solid #d9d9d9;
        padding: 0 10px;
        color: #333;
    }
    .agreement {
        font-size:14px;
        margin-top: 20px;
    }
    .mt-4 {
        margin-top:20px;
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

        &[disabled] {
            color: #ccc;
            background-color: #ffeba0;
        }
        &:not(:disabled):hover {
            background-color: #f5c423;
        }
    }
</style>