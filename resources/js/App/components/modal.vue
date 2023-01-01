<template>
    <Teleport to="#app">
        <div  class="modal__layer" v-if="isShowModal" ref="modal">
            <transition name="popup" mode="out-in">
                <div class="modal__dialog" :style="{'width': `${width}%`}" ref="modalWrapper">
                    <div class="modal__header">
                        <slot name="header"></slot>
                        <button class="modal__close" @click="hide">
                            <span >✕</span>
                        </button>
                    </div>
                    <div class="modal__content">
                        <slot></slot>
                    </div>

                    <div class="modal__actions">
                        <slot name="buttons"></slot>
                    </div>
                </div>
            </transition>
        </div>
    </Teleport>
</template>

<script setup>
    import {defineEmits, defineProps, onUnmounted, ref, watch} from "vue";

    const modalWrapper = ref(null);
    const emit = defineEmits(['update:isShowModal']);
    const props = defineProps({
        isShowModal: Boolean,
        width: {
            type: Number,
            default: 100
        }
    });

    watch(props.isShowModal, value => {
            if (value) {
                document.addEventListener('click', closeModal, {capture: true});
            } else {
                document.removeEventListener('click', closeModal, {capture: true});
            }
        }
    );

    const closeModal = event => {
        if (!modalWrapper.value.contains(event.target)) {
            emit('update:isShowModal', false);
            document.removeEventListener('click', closeModal, {capture: true});
        }
    };

    const hide = () => emit('update:isShowModal', false);

    onUnmounted(() => document.removeEventListener('click', closeModal, { capture: true }));
</script>

<style lang="scss" scoped>
    .modal {
        &__layer {
            background-color:rgba(33, 33, 33, 0.46);
            display:block;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 200;
            width: 100%;
            height: 100vh;
            overflow: hidden;
            outline: 0;
        }
        &__dialog {
            overflow:hidden;
            display: flex;
            flex-direction: column;
            max-height: 95%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border-radius:5px;
            background-color: #fff;
            & > div {
                flex: 0 0 auto;
            }
        }
        &__header {
            padding: 0 25px;
            display: flex;
            justify-content: space-between;
        }
        &__close {
            cursor: pointer;
            float: right;
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
            color: #000;
            text-shadow: 0 1px 0 #fff;
            opacity: .5;
            padding: 0;
            border-style: none;
            background-color: transparent;
        }
        &__content {
            flex: 0 1 auto;
            overflow-x:hidden;
            overflow-y: auto;
            padding: 0 25px;
        }
        &__actions {
            margin-top: 15px;
            margin-bottom: 25px;
            align-items: center;
            display: flex;
            justify-content: flex-end;
            padding: 0 25px;
        }
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
