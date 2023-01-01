<template>
    <modal
            :width="width"
            v-model:isShowModal="isShowImageLoadingFormLocal"
    >
        <template v-slot:header>
            <h2>Добавление фото или видео</h2>
        </template>

        <router-link
                :to="{name: (!isAuth || (isAuth && !existingReview)) ? 'add-photos' : 'add-review' }"
                class="add-photos__btn"
        >
            Добавить фотографии
        </router-link>

    </modal>
</template>

<script setup>
    import modal from '../../components/modal'
    import {onBeforeMount, defineProps, defineEmits, computed} from 'vue';
    import { storeToRefs } from "pinia";
    import {useReviewStore} from "../../store/review";
    import {useAuthStore} from "../../store/auth";

    const emit = defineEmits(['update:isShowImageLoadingForm']);
    const props = defineProps({
        isShowImageLoadingForm: {
            type: Boolean,
            default: false
        }
    });

    const reviewStore = useReviewStore();
    const authStore = useAuthStore();
    const { existingReview } = storeToRefs(reviewStore);
    const { isAuth } = storeToRefs(authStore);


    const isShowImageLoadingFormLocal = computed({
        get() {
            return props.isShowImageLoadingForm;
        },
        set(value) {
            emit('update:isShowImageLoadingForm', value);
        }
    });

    const width = computed(() => {
        const width = document.documentElement.clientWidth

        if (width < 1470 && width > 1200) {
            return 25;
        }
        if (width < 1200 && width > 900) {
            return 30;
        }
        if (width < 900 && width > 700) {
            return 50;
        }
        if (width < 700 && width > 500) {
            return 70;
        }

        if (width < 500) {
            return 95;
        }

        return 20;
    });

    onBeforeMount(() => {
        if (isAuth.value && existingReview.value === null) {
            reviewStore.checkExistingReview();
        }
    });
</script>

<style scoped lang="scss">
    .add-photos {
        &__btn {
            font-size: 16px;
            color: #222;
            background: #e8e8e8;
            border-radius: 4px;
            margin: 32px 0 12px;
            border: 0;
            position: relative;
            line-height: 48px;
            height: 48px;
            padding: 0 20px;
            display: inline-block;
            text-decoration: inherit;
            text-align: center;
        }
    }
</style>