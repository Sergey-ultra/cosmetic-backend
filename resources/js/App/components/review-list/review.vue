<template>
    <div class="review__main">
        <review-common :review="review"/>

        <div  class="review__bottom" v-if="review.plus && review.minus && review.comment">
            <div class="review__comment__btn" @click="toggleShowComments" v-if="review.comments_count">
                <span v-if="isShowComments">Скрыть комментарии</span>
                <span v-else>
                    {{ getCommentCountText(review.comments_count) }}
                </span>
            </div>

            <div class="review__comment__btn review__comment__btn-add" @click="toggleAddComment">
                Комментировать
            </div>

            <div class="dropdown">
                <drop-menu
                        :items="dropMenuItems"
                >
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

        <comment-list
                class="review__comments"
                v-if="isShowComments"
                :reviewId="review.id"
                :isShowComments="isShowComments"
        />
        <add-comment
                v-if="isAddComment"
                :reviewId="review.id"
        />
    </div>

    <modal
            :width="50"
            v-model:isShowModal="isShowComplaintForm"
    >
        <template v-slot:header>
            <h3>Жалоба на отзыв</h3>
        </template>
    </modal>

</template>

<script setup>
    import commentList from './comment-list'
    import addComment from './add-comment'
    import modal from '../modal'
    import dropMenu from '../drop-menu'
    import reviewCommon from '../review-common'
    import { ref, defineProps} from "vue";

    const props = defineProps({
        review: {
            type: Object
        }
    });

    let isShowComments = ref(false);
    let isAddComment = ref(false);
    let isShowComplaintForm = ref(false);
    const dropMenuItems =  ['complaint'];


    const toggleShowComments = () => isShowComments.value = !isShowComments.value;
    const toggleAddComment = () => isAddComment.value = !isAddComment.value;
    const showComplaintForm = () =>  isShowComplaintForm.value = true;
    const getCommentCountText = value => {
        switch (value) {
            case 1:
                return '1 комментарий';
            case 2:
            case 3:
            case 4:
                return '2 комментария';
            default:
                return `${value} комментариев`;
        }
    };
</script>

<style scoped lang="scss">
    .review {
        &__main {
            position: relative;
            width: 100%;
            padding-left: 48px;
            display: inline-block;
            vertical-align: top;
        }
        &__bottom {
            display: flex;
            flex-wrap: wrap;
            user-select: none;
            align-items: center;
            height: 36px;
            font-size: 14px;
            line-height: 16px;
        }
        &__comment__btn {
            cursor:pointer;
            color: #04b;
            font-size: 16px;
            line-height: 1;
            &:not(:first-child) {
                margin-left: 10px;
            }
            &-add {
                flex: 1 0 auto;
            }
            &:hover {
                color: #46bd87;
            }
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
                fill: rgba(0,0,0,.4);
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%,-50%);
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
    @media (max-width: 700px) {
        .review {
            &__avatar {
                position: static;
                transform: translateX(0);
            }
            &__main {
                padding: 0;
            }
            &__main__image {
                height: 200px;
            }
        }
    }
    @media (max-width: 500px) {
        .review {
            &__main__image {
                height: 150px;
            }
        }
    }
</style>