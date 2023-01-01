<template>
    <div class="light">
        <span class="light__close" @click="hide">✕</span>
        <div class="light__content">
            <div class="light__slider">
                <ul class="light__list">
                    <li
                            class="light__item"
                            :class="{'light__item-selected': selectedPhotoIndexLocal === index}"
                            v-for="(image, index) in smallImages"
                            :key="index"
                            @click="selectPhotoIndex(index)"
                    >
                        <img :src="image" >
                    </li>
                </ul>
            </div>
            <div class="light__main">
                <div class="light__inner">
                    <img :src="images[selectedPhotoIndexLocal]">
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import {defineProps, defineEmits, computed} from "vue";

    const emit = defineEmits(['update:selectedPhotoIndex', 'update:isShowLightBox']);
    const props = defineProps({
        isShowLightBox: {
            type: Boolean,
            default: false
        },
        selectedPhotoIndex: {
            type: Number,
            default: 0
        },
        images: {
            type: Array,
            default: () => []
        },
        smallImages: {
            type: Array,
            default: () => []
        }
    });

    const selectedPhotoIndexLocal = computed({
        get() {
            return props.selectedPhotoIndex;
        },
        set(value) {
            emit('update:selectedPhotoIndex', value)
        }
    });

    const hide = () => emit('update:isShowLightBox', false);
    const selectPhotoIndex = index => selectedPhotoIndexLocal.value = index;
</script>

<style scoped lang="scss">
    .light {
        background-color: #fff;
        display: block;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 200;
        width: 100%;
        height: 100vh;
        overflow: hidden;
        outline: 0;
        &__close {
            font-size: 35px;
            line-height: 35px;
            position: absolute;
            top: 25px;
            right: 35px;
            z-index: 101;
            width: 35px;
            height: 35px;
            cursor: pointer;
        }
        &__content {
            position: relative;
            display: flex;
            width: 100vw;
            height: 100vh;
        }

        &__slider {
            overflow-y: auto;
            width: 100px;
            margin: 20px 0;
            border-right: 1px solid #e9e9e9;
        }

        &__list {
            padding: 0;
            margin: 0;
            list-style: none;
        }
        &__item {
            width: 44px;
            height: 44px;
            margin: 0 auto 12px;
            position: relative;
            display: flex;
            padding: 6px;
            cursor: pointer;
            transition: border-color .15s ease-out;
            border: 2px solid #fff;
            border-radius: 2px;
            outline: none;

            &-selected {
                cursor: pointer;
                border-color: #ffdd60;
            }

            & img {
                position: absolute;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                max-width: 36px;
                max-height: 36px;
                margin: auto;
            }
        }
        &__main {
            flex: 1 1 auto;
            position: relative;
            z-index: 1;
            background-color: #fff;
        }
        &__inner {
            overflow: hidden;
            height: 100%;
            padding: 30px 100px 40px;
            user-select: none;
            text-align: center;
            transition-duration: 400ms;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 10;
            width: 100%;

            & img {
                max-width: 100%;
                max-height: 100%;
                position: absolute;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                margin: auto;
            }
        }
    }
</style>