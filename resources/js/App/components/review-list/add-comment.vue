<template>
    <form class="add-comment__form" @submit.prevent="send">
        <textarea class="textarea" v-model="commentField" name="comment" placeholder="Написать комментарий"></textarea>

        <button type="submit" class="btn">Добавьте коммент</button>
    </form>
</template>

<script setup>
    import {useAuthStore} from "../../store/auth";
    import {useCommentStore} from "../../store/comments";
    import {ref} from 'vue';
    import { storeToRefs } from "pinia";

    const commentField = ref('');

    const props = defineProps({
        reviewId: {
            type: Number
        }
    });

    const authStore = useAuthStore();
    const commentStore = useCommentStore();
    const { isAuth } = storeToRefs(authStore);

    const send = () => {
        if (commentField.value) {
            if (!isAuth.value) {
                authStore.setIsShowAuthModal(true);
            } else {
                commentStore.sendComment({
                    comment: commentField.value,
                    review_id: props.reviewId
                });
                commentField.value = '';
            }
        }
    };
</script>

<style scoped lang="scss">
    .add-comment__form {
        margin-top:15px;
        display:flex;
        flex-direction: column;
        align-items:flex-end;
    }
    .textarea {
        width: 100%;
        resize: vertical;
        outline: #000 none medium;
        overflow: visible;
        transition: background-color 0.3s ease 0s, border-color 0.3s ease 0s;
        border: 1px solid transparent;
        border-radius: 8px;
        padding: 8px;
        background-color: rgb(240, 242, 252);
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

    .btn {
        margin-top: 15px;
        color: #fff;
        background-color: #42b983;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        line-height: 36px;
        padding:0 20px;
    }
</style>