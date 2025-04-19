<template>
    <loader
        class="loader"
        v-if="isLoadingReviewData"
    />
    <div v-else>
        <form v-if="!isShowPublishedForm" class="form">
            <h2>{{ currentReviewData?.title }}</h2>
            <review-body v-model:body="currentReviewData.body"/>

            <div class="buttons">
                <button-component class="button" @click="setToArchive">В архив</button-component>
                <button-component class="button" @click="showPublishForm">Далее</button-component>
            </div>
        </form>
        <publish v-else></publish>
    </div>
</template>

<script setup>
import ButtonComponent from "../../components/button-component.vue";
import loader from "../../components/loader.vue";
import reviewBody from './src/review-body.vue'
import publish from './src/publish.vue'
import {computed, onMounted, ref, watch} from "vue";
import {useStore} from "vuex";
import {useRoute, useRouter} from "vue-router";

const store = useStore();
const route = useRoute();
const router = useRouter();

const isShowPublishedForm = ref(false);

const showPublishForm = () => isShowPublishedForm.value = true;
const setToArchive = async() => {
    await store.dispatch('reviewParser/setArchived', route.params.id);
    await router.push({ name: 'review-publishing-list' });
}


const currentReviewData = computed(() => store.state.reviewParser.currentReviewData);
const isLoadingReviewData = computed(() => store.state.reviewParser.isLoadingReviewData);

watch(currentReviewData.value, value => console.log(value));

onMounted(async() => {
    if (route.params.id) {
        await store.dispatch('reviewParser/loadReviewData', route.params.id);
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
