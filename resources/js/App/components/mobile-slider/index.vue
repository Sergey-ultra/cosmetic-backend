<template>
    <div class="slider" ref="slider">
        <div
            @touchstart="startMobile($event)"
            @touchmove="moveMobile($event)"
            @touchend="endMobile"
            :style="{'left': left + (index - selectedPhotoIndexLocal) * 100 + '%'}"
            :class="{'no-transition': left !== 0}"
            class="slider__inner"
            v-for="(image, index) in images"
            :key="index"
        >
            <img :src="image" >
        </div>
    </div>
</template>

<script setup>
    import {defineProps, defineEmits, ref, computed} from "vue";

    let left = ref(0);
    let shiftX = ref(0);

    const emit = defineEmits(['update:selectedPhotoIndex']);
    const props = defineProps({
        selectedPhotoIndex: {
            type: Number,
            default: 0
        },
        images: {
            type: Array,
            default: () => []
        },
    });

    const selectedPhotoIndexLocal = computed({
        get() {
            return props.selectedPhotoIndex;
        },
        set(value) {
            emit('update:selectedPhotoIndex', value);
        }
    });


    const startMobile = event => shiftX.value = event.changedTouches[0].pageX;

    const moveMobile = event => {
        left.value = (event.changedTouches[0].pageX - shiftX.value) * 100 / event.target.parentElement.offsetWidth;
    };

    const endMobile = () => {
        if (left.value < -10 && selectedPhotoIndexLocal.value < props.images.length - 1) {
            selectedPhotoIndexLocal.value++;
        } else if (left.value > 10 &&  selectedPhotoIndexLocal.value > 0) {
             selectedPhotoIndexLocal.value--;
        }
        left.value = 0;
    };
</script>

<style scoped lang="scss">
    .slider {
        width: 100%;
        overflow: hidden;
        height: 300px;
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
        .no-transition {
            transition: none;
        }
    }
</style>