<template>
    <loader
        class="loader"
        v-if="isLoadingReviewData"
    />
    <form v-else class="form">
        <h2>{{ currentReviewData?.title }}</h2>
        <add-new-sku v-if="currentSku === null" @setNewSku="setCurrentSku"/>
        <compactSku v-else :currentSku="currentSku"/>


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
import {useRoute} from "vue-router";
import useVuelidate from "@vuelidate/core";
import {helpers, maxLength, minLength, required} from "@vuelidate/validators";

const store = useStore();
const route = useRoute();

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
    }
};

const setCurrentSku = review => {
    currentSku.value =  review;
    editedReview.value.sku_id = review.sku_id;
};

const initEditedReview = () => {
    editedReview.value.blocks = currentReviewData.value.body && Object.keys(currentReviewData.value.body).length
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

</style>
