<template>
    <div class="relative">
        <div class="tool-tip" :style="positionStyle">
            <slot name="content"></slot>
        </div>
         <slot class="slot"> </slot>
    </div>
</template>

<script setup>
    import {defineProps, computed} from "vue";

    const props = defineProps({
        position: {
            type: String,
            default: 'right'
        }
    });

    const positionStyle = computed(() => {
        if (['right', 'left'].includes(props.position)) {
            return `top: 50%;transform: translateY(-50%);${props.position}: calc(100% + 2px);`
        } else if (['top', 'bottom'].includes(props.position)) {
            return ` left: 50%; transform: translateX(-50%);${props.position}: calc(100% + 2px);`
        }
    });

</script>

<style lang="scss" scoped>
    .relative {
        position: relative;

        &:hover .tool-tip {
            display:flex;
        }
    }

    .tool-tip {
        font-size: 15px;
        z-index:4;
        white-space: nowrap;
        padding: 5px 15px;
        display:none;
        position: absolute;
        background-color: #000;
        color: #fff;
        border-radius: 8px;
    }
    .slot {
        height:100%;
        width:100%;
    }
</style>
