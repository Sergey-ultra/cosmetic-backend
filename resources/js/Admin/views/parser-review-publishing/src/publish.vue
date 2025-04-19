<template>
    <loader
        class="loader"
        v-if="isLoadingReviewData"
    />
    <form v-else class="form">
        <h2>{{ currentReviewData?.title }}</h2>

        <div v-if="!currentSku">
            <div v-if="!isShowAddForm">
                <p class="item bold">Введите <span class="red">точное название</span> объекта отзыва, о котором хотите написать:</p>
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
                        <div class="suggest__link">
                            <div class="suggest__img" :style="`background-image: url(${sku.image})`"></div>
                            <div class="suggest__text">
                                <span class="suggest__title">{{`${sku.name} ${sku.volume}`}}</span>
                            </div>
                            <a :href="`/product/${sku.sku_code}`" target="_blank">
                                <svg
                                    height="16px"
                                    width="16px"
                                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    viewBox="0 0 26 26"
                                    xml:space="preserve">
                                <g>
                                    <path style="fill:#030104;" d="M18,17.759v3.366C18,22.159,17.159,23,16.125,23H4.875C3.841,23,3,22.159,3,21.125V9.875
                                        C3,8.841,3.841,8,4.875,8h3.429l3.001-3h-6.43C2.182,5,0,7.182,0,9.875v11.25C0,23.818,2.182,26,4.875,26h11.25
                                        C18.818,26,21,23.818,21,21.125v-6.367L18,17.759z"/>
                                    <g>
                                        <path style="fill:#030104;" d="M22.581,0H12.322c-1.886,0.002-1.755,0.51-0.76,1.504l3.22,3.22l-5.52,5.519
                                            c-1.145,1.144-1.144,2.998,0,4.141l2.41,2.411c1.144,1.141,2.996,1.142,4.14-0.001l5.52-5.52l3.16,3.16
                                            c1.101,1.1,1.507,1.129,1.507-0.757L26,3.419C25.999-0.018,26.024-0.001,22.581,0z"/>
                                    </g>
                                </g>
                            </svg>
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
            <add-new-sku v-if="isShowAddForm" @setCurrentSku="setCurrentSku" :categoryId="currentReviewData.category_id"/>
        </div>

        <compactSku v-else :currentSku="currentSku" @setCurrentSku="setCurrentSku"/>


        <review-body v-model:body="editedReview.body.blocks" isEditMode @toggleDisabledItem="toggleDisabledItem"/>


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

        <div class="form__group">
            <div class="label">
                <span class="text-gray">Порекоммендовали бы друзьям?</span>
            </div>
            <div>
                <radioComponent
                    v-model="editedReview.is_recommend"
                    :value="1"
                >
                    <span>Да</span>
                </radioComponent>
                <radioComponent
                    v-model="editedReview.is_recommend"
                    :value="0"
                >
                    <span>Нет</span>
                </radioComponent>
            </div>
        </div>

        <div class="buttons">
            <button-component class="button" @click="saveNewReview">Сохранить</button-component>
        </div>
    </form>
</template>

<script setup>
import compactSku from './compact-sku.vue'
import reviewBody from './review-body.vue'
import AddNewSku from "./add-new-sku.vue";
import ButtonComponent from "../../../components/button-component.vue";
import radioComponent from "../../../components/radioComponent.vue";
import inputComponent from '../../../components/input-component/index.vue';
import ratingForm from '../../../components/rating-form.vue';
import loader from "../../../components/loader.vue";

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

const toggleDisabledItem = index => {
    if (editedReview.value.body.blocks) {
        editedReview.value.body.blocks[index].disabled = !editedReview.value.body.blocks[index].disabled;
    }
};

const saveNewReview = async () => {
    const validated = await v$.value.editedReview.$validate();
    if (validated) {
        editedReview.value.body.blocks = editedReview.value.body.blocks.filter(el => !el.disabled);

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
    editedReview.value.body.blocks = currentReviewData.value.body && currentReviewData.value.body.length
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
    if (route.params.id && currentReviewData.value) {
        initEditedReview();
    }
});
</script>

<style lang="scss" scoped>
@import '@/Admin/scss/form.scss';
.flex {
    display: flex;
    gap: 15px;
}
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
        display: flex;
        align-items: center;
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
