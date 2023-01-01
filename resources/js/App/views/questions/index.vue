<template>
    <compact-sku>
        <div class="question">
            <h1 class="question__title">
                Есть вопрос о товаре?
            </h1>
            <div class="question__description" v-if="!total">
                Пока никто не задавал вопросов — станьте первым
            </div>
            <form id="scroll-to-question-form" @submit.prevent="sendQuestion" class="form">
                <div class="form__row">
                    <div class="form__group">
                        <textarea v-model="question" name="" id="" cols="30" rows="5" placeholder="Задать свой вопрос"></textarea>
                        <div class="invalid-feedback" v-for="error of v$.question.$errors" :key="error.$uid">
                            {{ error.$message }}
                        </div>
                    </div>
                </div>
                <div class="form__row">
                    <button type="submit" class="button button-form">
                        <span>Спросить</span>
                    </button>
                </div>
            </form>
            <div v-if="isLoadingQuestions" class="question__list">

            </div>
            <div v-else class="question__list">
                <h2 class="question__header">
                    {{ countText }}
                </h2>

                <div
                    v-for="question in questionWithPagination"
                    :key="question.id"
                    class="question__item"
                >
                    <div class="question__author">
                        <div>
                            <span class="question__user">{{ question.user_name }}</span>
                        </div>
                        <div class="question__date">{{ question.created_at }}</div>
                    </div>
                    <div class="question__body">
                        <h3>{{ question.body }}</h3>
                    </div>
                </div>

                <pagination
                    v-if="questionWithPagination.length && lastPage > 1"
                    v-model:currentPage="currentPageLocal"
                    :lastPage="lastPage"
                />
            </div>
        </div>
    </compact-sku>
</template>

<script setup>
    import compactSku from '../../components/compact-sku'
    import pagination from "../../components/pagination";
    import { onMounted, computed, ref, watch } from 'vue';
    import { storeToRefs } from "pinia";
    import useVuelidate from "@vuelidate/core";
    import {helpers, minLength, required} from "@vuelidate/validators";
    import {useQuestionStore} from "../../store/question";
    import {useAuthStore} from "../../store/auth";
    import {useCurrentSkuStore} from "../../store/currentSku";
    import {useRoute, useRouter} from "vue-router";

    const questionStore = useQuestionStore();
    const authStore = useAuthStore();
    const currentSkuStore = useCurrentSkuStore();
    const { questionWithPagination, isLoadingQuestions, tableOptions, total , lastPage } = storeToRefs(questionStore);
    const { isAuth } = storeToRefs(authStore);
    const { currentSkuId } = storeToRefs(currentSkuStore);

    let rules = {
        question: {
            required:  helpers.withMessage('Поле должно быть заполнено', required),
            minLength: minLength(2)
        }
    };
    let question = ref('');

    const v$ = useVuelidate(rules, question);

    const route = useRoute();
    const router = useRouter();

    const currentPageLocal = computed({
        get() {
            return tableOptions.value.currentPage;
        },
        set(value) {
            setPageQuery(value);
        }
    });



    const countText = computed(() => {
            let text = ''
            switch (total.value) {
                case 1:
                    text = 'вопрос';
                    break;
                case 2:
                case 3:
                case 4:
                    text = 'вопроса';
                    break;
                default:
                    text = 'вопросов';
            }
            return total.value + ' ' + text;
        }
    );

    const setPageQuery = value  => {
        const query =  { ...route.query }

        if (value > 1) {
            query.page = value
        } else if (value === 1) {
            delete query.page
        }
        router.push({ query })
    };

    const sendQuestion = async () => {
        if (!isAuth.value) {
            authStore.setIsShowAuthModal(true);
        } else {
            const validated = await v$.value.$validate();
            if (validated) {
                questionStore.createQuestion({body: question.value});
                v$.value.$reset();
                question.value = '';
            }
        }
    };


    watch(currentSkuId, value => {
        if (value) {
            questionStore.loadQuestionsWithPagination();
        }
    });

    watch(
        () => ({params: route.params, query: route.query}),
        value => {
            if (value) {
                questionStore.setTableOptionsByQuery(value.query);
                questionStore.loadQuestionsWithPagination();
            }
        },
        {deep: true}
    );

    onMounted(() => {
        questionStore.setTableOptionsByQuery(route.query);
        questionStore.loadQuestionsWithPagination();
    });
</script>

<style scoped lang="scss">
.question {
    width: 75%;
    padding: 36px 65px 3em 0;
    &__title {
        font-weight: 500;
        font-size: 22px;
        line-height: 28px;
        color: #222;
        margin: 0;
        padding: 0;
    }
    &__description {
        font-family: YS Text,sans-serif;
        font-weight: 400;
        font-style: normal;
        font-stretch: normal;
        font-size: 14px;
        line-height: 20px;
        margin-top: 20px;
    }
    &__header {
        font-weight: 500;
        font-size: 22px;
        line-height: 28px;
        margin-bottom: 24px;
    }
    &__author {
        overflow: hidden;
        margin-bottom: 4px;
        flex-wrap: wrap;
        align-items: center;
        display: flex;
        flex-grow: 1;
    }
    &__user {
        font-family: YS Text,sans-serif;
        font-weight: 700;
        font-style: normal;
        font-stretch: normal;
        font-size: 14px;
        line-height: 20px;
        color: #222;
    }
    &__date {
        color: #999;
        margin-left: 12px;
    }
    &__body {
        margin: 2px 0 4px;
    }
}
.form {
    width: 100%;
    &__row {
        display: flex;
        width: 100%;
        margin-top: 15px;
    }
    &__group {
        flex:1;
    }
}
textarea {
    width: 100%;
    resize: vertical;
    border-radius: 8px;
    border: 1px solid #d9d9d9;
    padding: 0 10px;
    color: #333;
    outline: none;
    &:hover {
        border-color: rgb(192, 201, 240);
        transition: border-color 0.3s ease 0s;
    }
    &:focus {
        background-color: white;
        border-color: rgb(59, 87, 208);
        transition: background-color 0.3s ease 0s, border-color 0.3s ease 0s;
    }
}
.button {
    height: 38px;
    background-color: #fc0;
    border: none;
    color: #333;
    border-radius: 8px;
    transition: background-color 0.3s ease 0s, border-color 0.3s ease 0s;
    padding: 8px 30px;
    font-size: 18px;
    &:hover {
        background-color: #f5c423;
    }
    &-form {
        margin-left: auto;
    }
}
.invalid-feedback {
    color: #fc675c;
}
@media (max-width: 800px) {
    .question {
        width: 100%;
        padding: 36px 0 3em 0;
    }
}
</style>
