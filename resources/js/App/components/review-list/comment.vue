<template>
    <div class="comment">
        <div class="comment__title">
            <div class="comment__avatar">
                <div class="comment__avatar-img">
                    <img v-if="comment.user_avatar" :src="comment.user_avatar" :alt="comment.user_avatar">
                    <img v-else src="/storage/icons/user_avatar.png" alt="avatar">
                </div>
            </div>
            <span class="comment__name">{{comment.user_name}}</span>
            <span class="comment__date">{{comment.created_at}}</span>
        </div>

        <div class="comment__body">
            <div class="comment__message">{{comment.comment}}</div>
            <div class="comment__bottom">
                <div class="comment__send" @click="toggleAnswerForm(comment)">
                    <span>+</span>
                    <span class="comment__send-dashed">Ответить</span>
                </div>


                <div class="dropdown">
                    <drop-menu :items="['complaint']">
                        <template v-slot:complaint>
                            <div class="dropdown__inner"  @click="showComplaintForm()">
                                <button class="dropdown__value">
                                    <span>Пожаловаться</span>
                                </button>
                            </div>
                        </template>
                    </drop-menu>
                </div>

                <div class="vote">
                    <div class="vote__up">
                        <button class="vote__btn">
                            <div class="vote__icon">
                                <svg>
                                    <use xlink:href="#icons_thumb-up">
                                        <symbol viewBox="0 0 24 24" id="icons_thumb-up"><path fill-rule="evenodd" d="M19.88 8.318c.61 0 1.103.495 1.103 1.107v.044a16.864 16.864 0 0 1-.82 4.253l-.002.009a16.834 16.834 0 0 1-2.716 5.076l-.002.002-.16.19H8.909V9.57l4.859-5.808.003-.004a4.77 4.77 0 0 0 .774-1.447l.002-.007a4.84 4.84 0 0 0 .235-1.22c.2-.2.474-.323.778-.323.569 0 .956.38 1.188 1.025.276.77.252 1.642.252 1.642a4.828 4.828 0 0 1-1.043 3l.002.002-1.6 1.889h5.522zM4 18.999V9.57h3.272V19H4z"></path></symbol>
                                    </use>
                                </svg>
                            </div>
                            <span class="vote__likes-plus">0</span>
                        </button>
                    </div>

                    <div class="vote__down">
                        <button class="vote__btn">
                            <div class="vote__icon">
                                <svg>
                                    <use xlink:href="#icons_thumb-down">
                                        <symbol viewBox="0 0 24 24" id="icons_thumb-down"><path fill-rule="evenodd" d="M4.102 15.682A1.105 1.105 0 0 1 3 14.575v-.044c.094-1.478.37-2.903.82-4.253l.002-.01a16.833 16.833 0 0 1 2.716-5.075l.002-.003.159-.19h8.375v9.43l-4.858 5.809-.003.004a4.77 4.77 0 0 0-.774 1.447l-.002.006a4.84 4.84 0 0 0-.235 1.22c-.2.2-.474.324-.778.324-.57 0-.957-.38-1.188-1.025-.276-.77-.252-1.642-.252-1.642 0-1.135.392-2.176 1.043-3l-.003-.002 1.6-1.889H4.103zM16.71 14.43V5h3.273v9.43H16.71z"></path></symbol>
                                    </use>
                                </svg>
                            </div>
                            <span class="vote__likes-minus">0</span>
                        </button>
                    </div>
                </div>
            </div>
            <transition name="comment__answer">
                <form v-show="comment.isShowAnswerForm" class="comment__form" @submit.prevent="send">
                    <textarea class="textarea" v-model="commentField" name="comment" placeholder="Написать ответ"></textarea>
                    <div class="comment__buttons">
                        <button @click="toggleAnswerForm(comment)" class="btn btn-cancel">Отменить</button>
                        <button type="submit" class="btn">Ответить</button>
                    </div>
                </form>
            </transition>
        </div>
        <div v-if="comment.children" class="comment__nested">
            <comment
                    v-for="child in comment.children"
                    :key="child.id"
                    :comment="child"
                    @toggleAnswerForm="toggleAnswerForm"
            />
        </div>
    </div>

    <modal
            :width="50"
            v-model:isShowModal="isShowComplaintForm"
    >
        <template v-slot:header>
            <h3>Жалоба на комментарий</h3>
        </template>
    </modal>
</template>

<script setup>
    import dropMenu from '../drop-menu'
    import modal from '../modal'
    import {defineProps, defineEmits, ref} from 'vue';
    import { storeToRefs } from "pinia";
    import {useAuthStore} from "../../store/auth";
    import {useCommentStore} from "../../store/comments";

    const emit = defineEmits(['toggleAnswerForm']);
    const props = defineProps({
        comment: Object
    });

    const commentField = ref('');
    const isShowComplaintForm = ref(false);


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
                    reply_id: props.comment.id,
                    review_id: props.comment.reviewId
                });
                commentField.value = '';
            }
        }
    };

    const toggleAnswerForm = comment => {
        if (!comment.isShowAnswerForm) {
            commentField.value = '';
        }
        emit('toggleAnswerForm', comment);
    };

    const showComplaintForm = () => isShowComplaintForm.value = true;
</script>

<style lang="scss" scoped>
    .comment {
        width: 100%;
        display: inline-block;
        vertical-align: top;
        &__title {
            margin: 12px 0;
            display: flex;
            align-items: center;
        }
        &__name{
            font-weight: 700;
        }
        &__date {
            margin-left:10px;
            color: #a9a9a9;
            font-size: 13px;
        }
        &__avatar {
            display: inline-block;
            vertical-align: top;
            width: 48px;
            &-img {
                overflow: hidden;
                border-radius: 50%;
                height: 36px;
                width: 36px;
                & img {
                    object-fit: cover;
                    width: 100%;
                    height:100%;
                }
            }
        }

        &__body {

        }
        &__bottom {
            user-select: none;
            align-items: center;
            height: 36px;
            font-size: 14px;
            line-height: 16px;
            display: flex;
            margin-top: 8px;
        }
        &__send {
            flex: 1 0 auto;
            cursor:pointer;
            color: #a9a9a9;
            font-size: 15px;
            line-height:1;
            &-dashed {
                margin-left: 5px;
                border-bottom: 1px dashed currentColor;
            }
        }
        &__buttons {
            margin-top: 15px;
        }
        &__form {
            margin-top:15px;
            display:flex;
            flex-direction: column;
            align-items:flex-end;
        }
        &__nested {
            margin-left: 30px;
        }
    }


    .dropdown {
        margin: -5px 5px;
        &__inner {
            font-size: 14px;
            line-height: 16px;
        }
        &__value {
            margin: 0;
            border: none;
            outline: none;
            outline-offset: 2px;
            background: transparent;
            display: block;
            width: 100%;
            padding: 8px 12px;
            color: #222;
            cursor: pointer;
            user-select: none;
            text-align: left;
        }
    }
    .vote {
        display: flex;
        white-space: nowrap;
        &__likes {
            &-plus {
                font-weight: 400;
                font-style: normal;
                font-stretch: normal;
                font-size: 14px;
                line-height: 20px;
            }
        }

        &__btn {
            line-height: 36px;
            height: 36px;
            padding: 0 10px 10px 12px;
            border: 0;
            border-radius: 4px;
            position: relative;
            background-color: transparent;

            & svg {
                fill: rgba(0, 0, 0, .4);
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 20px;
                height: 20px;
            }
        }

        &__icon {
            display: inline-block;
            vertical-align: middle;
            height: 32px;
            width: 30px;
        }
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
        color: #fff;
        background-color: #42b983;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        line-height: 30px;
        padding:0 15px;
        &:not(:last-child) {
            margin-right:15px;
        }
    }
    .btn-cancel {
        color: grey;
        background-color: #eee;
    }

</style>
