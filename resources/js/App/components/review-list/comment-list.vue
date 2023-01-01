<template>
    <div class="comments">
        <div class="comments__row" v-if="isLoadingComments">
            <loader class="loader"/>
        </div>
        <div
                class="comments__row"
                v-for="comment in comments"
                :key="comment.id"
        >
            <comment
                    :comment="comment"
                    @toggleAnswerForm="toggleAnswerForm"
            />
        </div>
    </div>
</template>

<script setup>
    import comment from './comment'
    import loader from '../loader'
    import {onBeforeMount, defineProps, defineEmits, watch} from 'vue';
    import { storeToRefs } from "pinia";
    import {useCommentStore} from "../../store/comments";


    const props = defineProps({
        reviewId: {
            type: Number
        },
        isShowComments: {
            type: Boolean,
            default: false
        }
    });

    const commentStore = useCommentStore();
    const { comments, isLoadingComments } = storeToRefs(commentStore);

    watch(isShowComments, async (value) => {
            if (value && !comments.value.length) {
                initLoad();
            }
        }
    );

    onBeforeMount(() => initLoad());


    const initLoad = async () => {
        await commentStore.loadComments(props.reviewId);
        setFalseToAnswerForms(comments.value);
    };
    //const toggleAddForm = () => {
    //     isShowAddForm.value = !isShowAddForm.value;
    //     setFalseToAnswerForms(comments.value);
    // },

    const toggleAnswerForm = comment => setAnswerForms(comments.value, comment.id);

    const setAnswerForms = (comments, id) => {
        comments.forEach(comment => {
            if (comment.id === id) {
                comment.isShowAnswerForm = !comment.isShowAnswerForm
            } else {
                comment.isShowAnswerForm = false
            }

            if (comment.children && Array.isArray(comment.children)) {
                this.setAnswerForms(comment.children, id)
            }
        })
    };

    const setFalseToAnswerForms = comments => {
        comments.forEach(comment => {
            comment.isShowAnswerForm = false

            if (comment.children && Array.isArray(comment.children)) {
                this.setFalseToAnswerForms(comment.children)
            }
        })
    };
</script>

<style lang="scss" scoped>
    .comments {
        &__row {
            padding-top: 20px;
            width: 100%;
        }
    }
    .loader {
        height: 70px;
        width: 70px;
    }
    .title {
        cursor:pointer;
        color: #04b;
        font-size: 16px;
        line-height: 1;
        &:hover {
             color: #46bd87;
        }
    }
</style>
