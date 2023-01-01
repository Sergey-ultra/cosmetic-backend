<template>
    <my-published
            @showEditForm="showEditForm"
            @showDeleteForm="showDeleteForm"
    >
        <template v-slot:status v-if="['moderated', 'rejected'].includes(comment.status)">
            <div class="status">
                <span v-if="comment.status === 'moderated'">Отзыв на модерации</span>
                <span v-else-if="comment.status === 'rejected'">Отзыв отклонен</span>
            </div>
        </template>

        <template v-slot:title>
            <div class="comment__title">
                <div class="comment__title-name">Отзыв на {{ comment.body }}</div>
            </div>
        </template>
        <div>{{ comment.comment }}</div>
    </my-published>

    <delete-form
            :selectedName="`Отзыв на ${comment.body}`"
            v-if="isShowDeleteForm"
            v-model:isShowDeleteForm="isShowDeleteForm"
            @delete="deleteCurrentComment"
    />
</template>

<script setup>
    import dropMenu from '../drop-menu'
    import deleteForm from '../delete-form'
    import ratingView from "../ratingView";
    import myPublished from "./my-published";
    import {ref, defineProps} from 'vue';
    import {useCommentStore} from "../../store/comments";

    const commentStore = useCommentStore();
    const isShowDeleteForm = ref(false);
    const isShowEditForm = ref(false);

    const props = defineProps({
        comment: {
            default: null
        }
    });


    const showDeleteForm = () => isShowDeleteForm.value = true;

    const deleteCurrentComment = () => {
        if (props.comment.comment_id) {
            commentStore.deleteItem(props.comment.comment_id);
        }
        isShowDeleteForm.value = false;
    };

    const showEditForm = () => {
        //this.$router.push({ name: 'edit-comment'})
    };
</script>

<style scoped lang="scss">
    .status {
        margin: -16px -16px 16px;
        padding: 14px 16px 14px 20px;
        background-color: #ffe1e1;
        font-size: 15px;
        line-height: 18px;
        & span {
            display: inline;
            color: #c70000;
            margin: 8px;
        }
    }
    a {
        text-decoration: none;
    }
    .comment {
        &__title {
            display: flex;
            align-items: center;
            letter-spacing: .1px;
            min-height: 40px;
            &-name {
                display: flex;
            }
        }
    }
</style>