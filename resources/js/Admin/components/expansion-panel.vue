<template>
    <div class="expansion">
        <div class="expansion-header" @click="toggleShowContent">
            <slot  class="expansion__toggle"></slot>
            <div class="expansion-header__icon">
                <span class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" role="img" aria-hidden="true" class="v-icon__svg">
                        <path d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z"></path>
                    </svg>
                </span>
            </div>
        </div>
        <transition v-if="isShowContent" name="popup" mode="out-in">
            <div class="expansion__content">
                <div class="expansion__wrap">
                    <slot name="content">

                    </slot>
                </div>

            </div>
        </transition>
    </div>
</template>

<script>
    export default {
        name: "expansion-panel",
        data: () => ({
            isShowContent: false
        }),
        methods: {
            toggleShowContent(){
                this.isShowContent = !this.isShowContent
            }
        }

    }
</script>

<style scoped lang="scss">
    .expansion {
        border-radius: 4px;
        background-color: #fff;
        color: rgba(0,0,0,.87);
        flex: 1 0 100%;
        max-width: 100%;
        position: relative;
        padding: 0;
        margin: 0;
        cursor: auto;
        transition: .3s cubic-bezier(.25,.8,.5,1);
        &:before {
            border-radius: inherit;
            bottom: 0;
            content: "";
            left: 0;
            position: absolute;
            right: 0;
            top: 0;
            z-index: -1;
            transition: box-shadow .28s cubic-bezier(.4,0,.2,1);
            /*will-change: box-shadow;*/
            background-repeat: no-repeat;
            box-sizing: inherit;
            box-shadow: 0 3px 1px -2px rgba(0,0,0,.2),0 2px 2px 0 rgba(0,0,0,.14),0 1px 5px 0 rgba(0,0,0,.12);
        }
        &-header {
            align-items: center;
            border-top-left-radius: inherit;
            border-top-right-radius: inherit;
            display: flex;
            font-size: .9375rem;
            line-height: 1;
            min-height: 48px;
            outline: none;
            padding: 16px 24px;
            position: relative;
            transition: min-height .3s cubic-bezier(.25,.8,.5,1);
            width: 100%;
            &:before {
                background-color: currentColor;
                border-radius: inherit;
                bottom: 0;
                content: "";
                left: 0;
                opacity: 0;
                pointer-events: none;
                position: absolute;
                right: 0;
                top: 0;
                transition: opacity .3s cubic-bezier(.25,.8,.5,1);
            }
            &__icon {
                margin-left: auto;
                display: inline-flex;
                margin-bottom: -4px;
                margin-top: -4px;
                user-select: none;
                padding: 0;
            }
        }
        &__wrap {
            padding: 0 24px 16px;
            flex: 1 1 auto;
            max-width: 100%;
        }
    }

    .icon {
        height: 24px;
        width: 24px;
    }

    .popup-enter-active,
    .popup-leave-active {
        transition: all 0.4s ease;
    }
    .popup-enter-from,
    .popup-leave-to {
        transform: translateY(-100px);
    }
</style>
