<template>
    <div class="review">
        <div  class="review__wrapper">
            <div class="review__inner">
                <div class="review__items">
                    <p v-for="(block, index) in body" :key="index" class="review__item">
                        <div v-if="block.type === 'paragraph'">
                              <textareaComponent v-model="block.data.text"></textareaComponent>
                        </div>
                        <img
                            v-else-if="block.type === 'image'"
                            :src="block.data.text"
                            :alt="block.data.description">
                        <div v-if="isEditMode" class="review__item-close" @click="emit('toggleDisabledItem',index)">×</div>
                        <div v-if="block.disabled" class="review__item-overlay"></div>
                    </p>
                </div>
            </div>

        </div>
    </div>
</template>
<script setup>
import textareaComponent from '../../../components/textarea-component.vue'
import {computed, defineProps, defineEmits} from "vue";

const emit = defineEmits(['toggleDisabledItem']);
const props = defineProps({
        body: {
            type: Array,
            default: () => [],
        },
        isEditMode: {
            type: Boolean,
            default: false,
        }
    });

</script>
<style scoped lang="scss">
$greenColor: #46bd87;

.recommend-ratio {
    margin-bottom: 15px;
    color: $greenColor;
    & > span {
        font-size: larger;
        font-weight: bold;
    }
}

.loader {
    width: 200px;
    height: 200px;
}

.title {
    font-weight: 500;
    font-size: 30px;
    line-height: 56px;
    margin: 16px 0 0;
    color: #222;
    max-width: 920px;
}

.review {
    width: 100%;
    background-color: #fff;
    padding:25px;
    border-radius: 8px;
    margin-top: 60px;
    &__wrapper {
        display:flex;
    }
    &__inner {
        padding-right: 65px;
    }
    &__right {
        flex-grow: 1;
        margin-bottom: 8px;
        max-width: 336px;
        max-height: 300px;
        overflow: hidden;
    }
    &__top {
        display: flex;
    }
    &__item {
        margin: 12px 0;
        color: #2b2b2b;
        display: block;
        position: relative;
        &:hover .review__item-close {
            display: flex;
        }
        &-close {
            z-index:1;
            position: absolute;
            top: 0;
            right: 0;
            overflow: hidden;
            transition: color .1s ease-out;
            border: none;
            border-radius: 0;
            outline: initial;
            background: 0 0;
            font-family: serif;
            font-size: 30px;
            cursor: pointer;
            color: #000;
            display: none;
            justify-content: center;
            align-items:center;
            margin: 10px 10px 0 0;
            height: 15px;
            width: 15px;
        }
        &-overlay {
            position: absolute;
            top: 0;
            bottom: 0;
            right: 0;
            left: 0;
            background: #000;
            opacity: .6;
        }

        img {
            max-width: 100%;
            max-height: 100%;
        }

        & dt {
            font-size: 16px;
            line-height: 24px;
            font-weight: 700;
            display: inline-block;
            margin-right: 5px;
        }

        & dd {
            margin: 0;
            display: inline;
            max-width: 100%;
            word-wrap: break-word;
            word-break: break-word;
            white-space: pre-line;
        }
    }
    &__image {
        height: 200px;
        width: 200px;
        & img {
            max-height: 100%;
            max-width: 100%;
        }
    }
    &__ratings {
        width: 100%;
        display: flex;
        flex-wrap: wrap;
    }
    &__rating {
        margin-bottom: 15px;
        display: flex;
        flex-direction: column;
        margin-right: 10px;
        &-text {
            font-size: 35px;
        }
        &-precise {
            margin-top: auto;
        }
    }
    &__percentage {
        margin-bottom: 15px;
    }
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
            color: $greenColor;
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
@media (max-width: 900px) {
    .review {
        &__inner {
            width: 100%;
            padding: 0;
        }
    }
}
@media (max-width: 500px) {
    .title {
        font-weight: 500;
        font-size: 20px;
        line-height: 35px;
    }
    .review {
        &__main__image {
            height: 150px;
        }
    }
}
</style>
