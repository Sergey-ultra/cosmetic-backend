<template>
    <button
        type="button"
        class="button"
        :disabled="disabled"
        :class="{
            [`button--${color}`]: Boolean(color),
            [`button--${size}`]: Boolean(size),
            'button--icon': icon,
            'button--outline': outline,
            'button--round': round,
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
        },
        /**
         * Стиль иконка
         */
        icon: {
            type: Boolean,
            default: false,
        },
        /**
         * Стиль с обводкой
         */
        outline: {
            type: Boolean,
            default: false,
        },
        /**
         * Стиль скругленный
         */
        round: {
            type: Boolean,
            default: false,
        },

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
        border: 0 solid #616469 !important;
        a {
            color: inherit;
        }
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
        &--icon {
            padding-left: 0;
            padding-right: 0;
            text-align: center;
        }
        &.button--icon {
            padding-left: 0;
            padding-right: 0;
            width: 32px !important;
        }
        &.button--round {
            border-radius: 50%;
        }
        &.button--outline {
            background: hsla(220,4%,50%,.05) !important;
            border-color: #7a7d83 !important;
            box-shadow: none;
            color: #7a7d83 !important;
            line-height: 30px;
            text-shadow: none !important;
            border-width: 1px !important;
        }
        &--small {
            min-width: 28px;
            height: 28px;
            padding: 0 12.4px;
        }
        &--yellow {
            background-color: #fc0;
            color: #000000;
        }
        &--grey {
            background: #e0e0e0 !important;
            border-color: #7e7c7c !important;
            box-shadow: 0 0 2px rgba(0,0,0,.05),0 1px 1px rgba(0,0,0,.1);
            color: #fff !important;
            text-shadow: none !important;
        }
        &--blue {
            background-color: rgb(0, 91, 255);
        }
        &--green {
            background: #3fa149 !important;
            border-color: #317c38 !important;
            box-shadow: 0 0 2px rgba(0,0,0,.05),0 1px 1px rgba(0,0,0,.1);
            color: #fff !important;
            text-shadow: none !important;

            &.button--outline {
                background: rgba(63,161,73,.05) !important;
                border-color: #3fa149 !important;
                box-shadow: none;
                color: #3fa149 !important;
                text-shadow: none !important;
                &:hover {
                    background: #4bb956 !important;
                    border-color: #4bb956 !important;
                    box-shadow: none;
                    color: #fff !important;
                    text-shadow: none !important;
                }
            }
        }
        &--red {
            background: #d8443a !important;
            border-color: #ba2e25 !important;
            box-shadow: 0 0 2px rgba(0,0,0,.05),0 1px 1px rgba(0,0,0,.1);
            color: #fff !important;
            text-shadow: none !important;
            &.button--outline {
                background: rgba(216, 68, 58, .05) !important;
                border-color: #d8443a !important;
                box-shadow: none;
                color: #d8443a !important;
                text-shadow: none !important;

                &:hover {
                    background: #de6058 !important;
                    border-color: #de6058 !important;
                    box-shadow: none;
                    color: #fff !important;
                    text-shadow: none !important;
                }
            }
        }
    }


    .loader {
        position: absolute;
        right: 0;
    }
</style>
