<template>
    <button
        type="button"
        class="button"
        :disabled="disabled"
        :class="{
            [`${color}`]: Boolean(color),
            [`${size}`]: Boolean(size),
        }"
    >
        <slot></slot>
        <loader :color="'#fff'" class="loader" v-if="isLoading"/>
    </button>
</template>

<script>
import loader from "./loader.vue"
export default {
    name: "button-component",
    components: {
        loader,
    },
    props: {
        isLoading: {
            type: Boolean,
            default: false
        },
        disabled: {
            type: Boolean,
            default: false
        },
        size: {
            type: String,
        },
        color: {
            type: String,
        }
    },
    computed: {

    }
}
</script>

<style lang="scss" scoped>
    .button {
        transition: all 0.12s ease-out;
        min-width: 35px;
        height: 35px;
        padding: 0 20px;
        display: flex;
        justify-content: center;
        position: relative;
        align-items: center;
        border-radius: 4px;
        color: #fff;
        background: rgb(24, 103, 192) none repeat scroll 0% 0%;
        border: 0;
        &:before {
            background-color: currentColor;
            border-radius: inherit;
            bottom: 0;
            color: inherit;
            content: "";
            left: 0;
            opacity: 0;
            pointer-events: none;
            position: absolute;
            right: 0;
            top: 0;
            transition: opacity .2s cubic-bezier(.4,0,.6,1);
        }
        &:hover::before {
            opacity: .08;
        }
        &[disabled] {
            border: none;
            background-color: rgba(0,0,0,.12);
            color: rgba(0,0,0,.26);
        }
    }
    .small {
        min-width: 28px;
        height: 28px;
        padding: 0 12.4px;
    }
    .yellow {
        background-color: #fc0;
        color: #000000;
    }
    .blue {
        background-color: rgb(0, 91, 255);
    }

    .loader {
        position: absolute;
        right: 0;
    }
</style>
