<template>
    <reviewsAnswersNav></reviewsAnswersNav>
    <div class="loader__wrapper" v-if="isLoadingMyComments">
        <loader class="loader"></loader>
    </div>
    <h4 v-if="!myComments.length">
        У вас пока нет комментариев
    </h4>
    <my-comment
            v-for="comment in myComments"
            :key="comment.id"
            :comment="comment"
    />
</template>

<script setup>
    import reviewsAnswersNav from "../../components/profile/reviewsAnswersNav";
    import myComment from "../../components/profile/my-comment";
    import loader from "../../components/loader";
    import { storeToRefs } from "pinia";
    import { onMounted } from 'vue';
    import {useCommentStore} from "../../store/comments";

    const commentStore = useCommentStore();
    const { isLoadingMyComments, myComments } = storeToRefs(commentStore);

    onMounted(() => {
        commentStore.loadMyComments();
    });
</script>

<style scoped lang="scss">
    .loader {
        width: 100px;
        height: 100px;
        &__wrapper {
            display: flex;
            justify-content: center;
            align-items:center;
        }
    }
</style>