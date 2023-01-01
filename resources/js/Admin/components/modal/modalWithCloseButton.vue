<template>
    <div  class="modal-layer" v-if="modelValue" ref="modalWrapper">
        <transition name="popup" mode="out-in">
            <div class="modal-body">
                <div class="modal-dialog" :style="{'width': `${width}%`}">
                    <slot name="header"></slot>
                    <div class="modal-content">
                        <slot name="content"></slot>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
    export default {
        name: "modalWithCloseButton",
        props: {
            modelValue: Boolean,
            width: Number
        },
    }
</script>

<style scoped>
    .modal-layer {
        background-color:rgba(33, 33, 33, 0.46);
        display:block;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 70;
        width: 100%;
        height: 100%;
        overflow: hidden;
        outline: 0;
    }

    .popup-enter-active,
    .popup-leave-active {
        transition: all 0.4s ease;
    }
    .popup-enter-from,
    .popup-leave-to {
        transform: translateY(-100px);
    }
    .modal-dialog {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        border-radius:5px;
        padding: 25px;
        background-color: #fff;
    }
</style>
