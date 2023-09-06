<template>
    <loader
        class="loader"
        v-if="isLoadingReviewData"
    />
    <form v-else class="form">
        <h2>{{ currentReviewData?.title }}</h2>

        <div v-if="!isShowAddForm">
            <div class="item flex">
                <inputComponent v-model.trim="search" :color="'white'" :isLoading="isLoadingSuggests" @input="getSuggests"/>
                <buttonComponent @click="showAddForm">
                    Далее
                </buttonComponent>
            </div>

            <ul class="suggest__content" v-if="search">
                <li
                    v-for="(sku, index) in suggestSkus"
                    :key="index"
                    @click="setCurrentSku(sku)"
                    class="suggest__item">
                    <div  class="mini-suggest__link">
                        <a :href="`/product/${sku.sku_code}`" class="suggest__img" :style="`background-image: url(${sku.image})`"></a>
                        <a :href="`/product/${sku.sku_code}`" class="suggest__text">
                            <span class="suggest__title">{{`${sku.name} ${sku.volume}`}}</span>
                        </a>
                    </div>
                </li>
            </ul>
        </div>
        <add-new-sku v-if="isShowAddForm" @setNewSku="setCurrentSku"/>
        <compactSku v-if="currentSku" :currentSku="currentSku"/>


        <review-body :currentReviewData="currentReviewData"/>

        <div class="invalid-feedback" v-for="error of v$.editedReview.sku_id.$errors" :key="error.$uid">
            {{ error.$message }}
        </div>

        <div class="form__group">
            <h4>Оценка модели</h4>
            <div>
                <ratingForm v-model="editedReview.rating"/>
                <div class="invalid-feedback" v-for="error of v$.editedReview.rating.$errors" :key="error.$uid">
                    {{ error.$message }}
                </div>
            </div>
        </div>

        <div class="form__group">
            <label>
                <div class="label">
                    <span class="text-gray">Ваше общее впечатление в двух словах: (от 1 до 15 слов)</span>
                </div>
                <inputComponent v-model="editedReview.title" :color="'white'"/>
            </label>

            <div class="invalid-feedback" v-for="error of v$.editedReview.title.$errors" :key="error.$uid">
                {{ error.$message }}
            </div>
        </div>

        <div class="form__group">
            <label>
                <div class="label">
                    <span class="text-gray">Достоинства</span>
                </div>
                <inputComponent v-model.trim="editedReview.plus" :color="'white'"/>
            </label>

            <div class="invalid-feedback" v-for="error of v$.editedReview.plus.$errors" :key="error.$uid">
                {{ error.$message }}
            </div>
        </div>

        <div class="form__group">
            <label>
                <div class="label">
                    <span class="text-gray">Недостатки</span>
                </div>
                <inputComponent v-model.trim="editedReview.minus" :color="'white'"/>
            </label>

            <div class="invalid-feedback" v-for="error of v$.editedReview.minus.$errors" :key="error.$uid">
                {{ error.$message }}
            </div>
        </div>

        <div class="buttons">
            <button-component class="button" @click="saveNewReview">Сохранить</button-component>
        </div>
    </form>
</template>

<script setup>
import compactSku from './src/compact-sku.vue'
import ButtonComponent from "../../components/button-component.vue";
import inputComponent from '../../components/input-component/index.vue';
import ratingForm from '../../components/rating-form.vue';
import loader from "../../components/loader.vue";
import reviewBody from './src/body.vue'
import AddNewSku from "./src/add-new-sku.vue";

import {computed, onMounted, ref} from "vue";
import {useStore} from "vuex";
import {useRoute, useRouter} from "vue-router";
import useVuelidate from "@vuelidate/core";
import {helpers, maxLength, minLength, required} from "@vuelidate/validators";

const store = useStore();
const route = useRoute();
const router = useRouter();

const search = ref('');
const isShowAddForm = ref(false);
const currentSku = ref(null);
const editedReview = ref({
    rating: 0,
    title: '',
    body: {
        blocks: [
            {
                type: 'paragraph',
                data: {
                    text: ''
                }
            }
        ],
    },
    plus: '',
    minus: '',
    is_recommend: 1,
});

const currentReviewData = computed(() => store.state.reviewParser.currentReviewData);
const isLoadingReviewData = computed(() => store.state.reviewParser.isLoadingReviewData);
const suggestSkus = computed(() => store.state.sku.suggestSkus);
const isLoadingSuggests = computed(() => store.state.sku.isLoadingSuggests);

const mustBeRating = value => value > 0;

const rules = {
    editedReview: {
        rating: {
            mustBeRating: helpers.withMessage('Нужно оценить товар', mustBeRating),
        },
        title: {
            required: helpers.withMessage('Поле должно быть заполнено', required),
            minLength: minLength(5),
            maxLength: maxLength(256),
        },
        plus: {
            required: helpers.withMessage('Поле должно быть заполнено', required),
            minLength: minLength(5),
        },
        minus: {
            required: helpers.withMessage('Поле должно быть заполнено', required),
            minLength: minLength(5),
        },
        sku_id: {
            required: helpers.withMessage('Должно быть связанное товарное предложение', required),
        },
    },
};

const v$ = useVuelidate(rules, { editedReview });

const saveNewReview = async () => {
    const validated = await v$.value.editedReview.$validate();
    if (validated) {
        await store.dispatch('review/createItem', editedReview.value);
        v$.value.$reset();
        await store.dispatch('reviewParser/setPublished', route.params.id);
        await router.push({ name: 'review-publishing-list' });
    }
};

const getSuggests = () => {
    if (search.value) {
        store.dispatch('sku/getSuggests', search.value);
    } else {
        store.dispatch('sku/setSuggestsToDefault');
    }
}

const showAddForm = () => isShowAddForm.value = true;



const setCurrentSku = review => {
    isShowAddForm.value = false;
    currentSku.value =  review;
    editedReview.value.sku_id = review.sku_id;
};

const initEditedReview = () => {
    editedReview.value.blocks = currentReviewData.value.body && currentReviewData.value.body.length
        ? currentReviewData.value.body
        : [
            {
                type: 'paragraph',
                data: {
                    text: ''
                }
            }
        ];
};


onMounted(async() => {
    if (route.params.id) {
        await store.dispatch('reviewParser/loadReviewData', route.params.id);
        if (currentReviewData.value) {
            initEditedReview();
        }
    }
});
</script>

<style lang="scss" scoped>
@import '@/Admin/scss/form.scss';
.form {
    max-width:1000px;
    background: inherit;
    margin-bottom:50px;
}


.buttons {
    height:35px;
    margin-top:15px;
    margin-bottom:25px;
    align-items: center;
    display: flex;
    justify-content: flex-end;
    padding: 0 25px;
}
.button:not(:last-child) {
    margin-right: 15px;
}

.input-group {
    width: 100%;
}
.form-control {
    width: 100%;
}


.invalid-feedback {
    color: red;
}


.suggest {
    &__content {
        padding: 3px 0;
        display: block;
        list-style: none;
        margin: 0;
        width:100%;
    }
    &__item {
        margin: 0 3px;
        border-radius: 7px;
        height: auto;
        &:hover {
            background-color: #fef5da;
        }

    }
    &__link {
        width: 100%;
        padding: 8px 13px;
        color: #000;
        display: block;
        outline: none;
        text-decoration: none;
    }
    &__img {
        background-position: 50%;
        background-repeat: no-repeat;
        border-radius: 4px;
        display: inline-block;
        position: relative;
        vertical-align: middle;
        height: 40px;
        margin-right: -40px;
        width: 40px;
        background-size: contain;
        & img {
            max-width:100%;
            max-height:100%;
        }
    }
    &__text {
        padding-left: 52px;
        display: inline-flex;
        flex-direction: column;
        justify-content: center;
        min-height: 40px;
        vertical-align: middle;
        width: 100%;
    }
    &__title {
        color: #222;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    &__info {
        color: grey;
        font-size: 14px;
        font-weight: 400;
        line-height: 20px;
    }
}
</style>
