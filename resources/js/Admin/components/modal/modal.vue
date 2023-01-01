<template>
    <Teleport to="#admin">
        <div  class="modal__layer" v-if="isShowForm" ref="modal">
            <transition name="popup" mode="out-in">
                <div class="modal__dialog" :style="{'top': `${top}%`, 'left': `${left}%`,'width': `${width}%`}"
                     ref="modalWrapper">
                    <div
                            class="modal__header"
                            @mousedown.prevent="move($event)"
                    >
                    <span class="modal__title">
                         <slot name="header"></slot>
                    </span>

                        <button class="modal__close" @click="hide">
                            <span>✕</span>
                        </button>
                    </div>
                    <div class="modal__content">
                        <slot></slot>
                    </div>
                    <div class="modal__actions">
                        <slot name="buttons"></slot>
                    </div>
                    <div class="modal__loading" v-if="isLoading">
                        <loader class="loader"/>
                    </div>
                </div>
            </transition>
    </div>
    </Teleport>
</template>

<script>
    import loader from '../loader.vue'

    export default {
        name: "modal",
        components: {
            loader
        },
        data() {
            return {
                left: 50,
                top: 50
            }
        },
        props: {
            isShowForm: Boolean,
            isLoading: {
              type: Boolean,
              default: false
            },
            width: {
                type: Number,
                default: 100
            }
        },
        watch:{
            isShowForm(value){
                if (value){
                    document.addEventListener('click', this.closeModal, { capture: true })
                } else {
                    document.removeEventListener('click', this.closeModal, { capture: true })
                }
            }
        },
        unmounted() {
            document.removeEventListener('click', this.closeModal, { capture: true })
        },
        methods: {
            closeModal(event) {
                if (!this.$refs.modalWrapper.contains(event.target)) {
                    this.$emit('update:isShowForm', false)
                    document.removeEventListener('click', this.closeModal, { capture: true })
                }
            },
            hide() {
                this.$emit('update:isShowForm', false)
            },
            move(event) {
                let shiftX =  event.pageX
                let shiftY =  event.pageY
                let left = this.left
                let top = this.top
                let parentElement = event.target.parentElement.parentElement



                let moving = event => {
                    let leftOffset = ((event.pageX - shiftX) / parentElement.offsetWidth) *100
                    let topOffset = ((event.pageY - shiftY) / parentElement.offsetHeight) *100

                    this.left = left + leftOffset
                    this.top = top + topOffset
                }

                document.onmousemove = function(event){
                    moving(event)
                }


                //   отследить окончание переноса
                document.onmouseup = function () {
                    document.onmousemove = null
                    document.onmouseup = null
                }
            },
        }
    }
</script>

<style lang="scss" scoped>
    .popup-enter-active,
    .popup-leave-active {
        transition: all 0.4s ease;
    }
    .popup-enter-from,
    .popup-leave-to {
        transform: translateY(-100px);
    }

    .modal {
        &__layer {
            background-color:rgba(33, 33, 33, 0.46);
            display:block;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 99;
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

        }
        &__header {
            cursor:grab;
            padding: 0 25px;
            background-color: #d6e4ff;
            display: flex;
            justify-content: space-between;
        }
        &__title {
            font-size:18px;
            font-weight: bold;
            margin: 10px 0;
        }
        &__close {
            width: 30px;
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
            height:35px;
            margin-top:15px;
            margin-bottom:25px;
            align-items: center;
            display: flex;
            justify-content: flex-end;
            padding: 0 25px;
        }
        &__loading {
            position: absolute;
            z-index: 2;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.82);
            display: flex;
            justify-content: center;
            align-items:center;
        }
    }
    .loader {
        width: 200px;
        height: 200px;
    }
</style>
