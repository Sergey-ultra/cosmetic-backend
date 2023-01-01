<template>
    <div
        v-if="!isMobileScreen"
        class="slider"
        @mousemove.prevent="photos.length > 1 ? selectPhotoIndex($event) : ''"
    >
        <img
            v-lazyload
            :data-src="photos[selectedPhotoIndex]"
            :alt="alt"
        >
        <ul class="slider__choice" v-if="photos.length > 1">
            <li
                class="slider__item"
                :class="{'slider__item-selected': selectedPhotoIndex === index}"
                v-for="(photo, index) in photos"
                :key="photo"
            ></li>
        </ul>
    </div>
    <div v-else class="mobile">
        <div
            @touchstart="startMobile($event)"
            @touchmove="moveMobile($event)"
            @touchend="endMobile"
            :style="{'left': left + (index - selectedPhotoIndex) * 100 + '%'}"
            :class="{'no-transition': left !== 0}"
            class="mobile__inner"
            v-for="(image, index) in mobilePhotos"
            :key="index"
        >
            <img
                v-lazyload
                :data-src="image"
                :alt="`${alt}_${index}`"
            >
        </div>
        <div class="mobile__dots" v-if="mobilePhotos.length > 1">
            <div
                class="mobile__dot"
                :class="{'mobile__dot-selected': index === selectedPhotoIndex}"
                v-for="(_, index) in mobilePhotos"
                :key="index"
            >
            </div>
        </div>
    </div>
</template>

<script setup>
    import {ref, computed, defineProps} from "vue";

    let selectedPhotoIndex = ref(0);
    let left = ref(0);
    let shiftX = ref(0);


    const props = defineProps({
        photos: {
            type: Array,
            default: () => []
        },
        alt: String
    });


    const isMobileScreen = computed(() => document.documentElement.clientWidth < 800);
    const mobilePhotos = computed(() => props.photos.slice(0,3));


    const getCoordsX = elem => {
        let box = elem.getBoundingClientRect();
        //возвращает координаты левого края элемента относительно viewport + отступ от края window
        return box.left + pageXOffset;
    };

    const selectPhotoIndex = event => {
        const element = event.target;
        const widthPart = element.offsetWidth / props.photos.length;
        const shiftX = event.pageX - getCoordsX(element);
        selectedPhotoIndex.value = Math.trunc(shiftX / widthPart);

        element.onmouseout = () => {
            selectedPhotoIndex.value = 0;
            element.onmouseout = null;
        }
    };

    const startMobile = event => shiftX.value = event.changedTouches[0].pageX;

    const moveMobile = event => {
        left.value = (event.changedTouches[0].pageX - shiftX.value) * 100 / event.target.parentElement.offsetWidth
    };

    const endMobile = () => {
        if (left.value < -10 && selectedPhotoIndex.value < mobilePhotos.value.length - 1) {
            selectedPhotoIndex.value++;
        } else if (left.value > 10 && selectedPhotoIndex.value > 0) {
            selectedPhotoIndex.value--;
        }
        left.value = 0;
    };
</script>

<style lang="scss" scoped>
    .slider {
        position: relative;
        &:hover .slider__choice {
            opacity: 1;
        }
        &__choice {
            list-style: none;
            position: absolute;
            top: 100%;
            width: 100%;
            padding: 0;
            margin:0;
            display: flex;
            opacity: 0;
        }
        &__item {
            flex-grow: 1;
            position: relative;
            height: 2px;
            border-radius: 4px;
            margin: 0 2px;
            background: #000;
            opacity: .06;
            transition: opacity 50ms;
            transition-timing-function: cubic-bezier(.65,.05,.36,1);
            &-selected {
                opacity: .33;
            }
        }
        & img {
            max-height: 100%;
            max-width: 100%;
        }
    }

    .mobile {
        width: 100%;
        overflow: hidden;
        height: 100%;
        margin: 20px 0;
        background: #fff;
        display: flex;
        align-items: center;
        flex-shrink: 0;
        position: relative;

        &__inner {
            transition: left 0.3s ease;
            position: absolute;
            top: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            width: 100%;

            & img {
                max-width: 100%;
                max-height: 100%;
            }
        }
        &__dots {
            position: absolute;
            bottom: 5px;
            right: 5px;
            display: flex;
            align-items: center;
        }
        &__dot {
            height: 5px;
            width: 5px;
            border-radius: 50%;
            background-color: #ccc;
            &:not(:last-child) {
                margin-right: 6px;
            }
            &-selected {
                height: 7px;
                width: 7px;
                background-color: #000;
            }
        }


        .no-transition {
            transition: none;
        }
    }
</style>