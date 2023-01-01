<template>
    <div class="rating">
        <div class="rating__body">
            <div class="rating__active" :style="{width:`${rating / 0.05}%`}"></div>
        </div>
    </div>
</template>

<script setup>
    import {defineProps} from "vue";

    const props = defineProps({
        rating: {
            default: 5
        }
    });
</script>

<style lang="scss" scoped>
.rating {
    display:inline-block;
    font-size: 18px;
    line-height: 0.75;
    &__body {
        position:relative;
        &::before {
            content:"★★★★★";
            display:block;
            color: grey;
        }
    }
    &__active {
        position: absolute;
        height: 100%;
        top: 0;
        left:0;
        overflow:hidden;
        &::before {
            content:"★★★★★";
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left:0;
            color: #ffda66;
        }
    }
}
</style>