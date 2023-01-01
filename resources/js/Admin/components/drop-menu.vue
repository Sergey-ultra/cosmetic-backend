<template>
    <div class="drop-menu">
        <button class="drop-menu__button" @click="toggleShowMenu">
            <svg class="drop-menu__svg">
                <use xlink:href="#icons_more-horiz">
                <symbol viewBox="0 0 24 24" id="icons_more-horiz"><path fill-rule="evenodd" d="M4 14a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm8 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm8 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"></path></symbol>
                </use>
            </svg>
        </button>
        <ul class="drop-menu__list" v-show="isShowMenu" ref="dropdownMenu">
            <li
                    class="drop-menu__item"
                    v-for="item in items"
                    :key="item"
            >
                <slot :name="item"/>
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        name: "drop-menu",
        data() {
            return {
                isShowMenu: false
            }
        },
        props:{
            items: {
                type: Array,
                default: () => []
            }
        },
        watch: {
            isShowMenu(value) {
                if (value) {
                    document.addEventListener('click', this.outsideClick, {capture: true, once: true})
                }
            }
        },
        methods: {
            toggleShowMenu() {
                this.isShowMenu = ! this.isShowMenu
            },
            outsideClick(event) {
                if (!this.$refs.dropdownMenu.contains(event.target)) {
                    event.stopPropagation()
                }
                document.removeEventListener('click', this.outsideClick, { capture: true })
                this.isShowMenu = false
            }
        }
    }
</script>

<style scoped lang="scss">
.drop-menu {
    font-size: 14px;
    line-height: 16px;
    position: relative;
    &__button {
        cursor: pointer;
        opacity: .3;
        display: block;
        background: none;
        border: 0;
        padding: 0;
        outline: none;
        height: 30px;
        &:hover {
            opacity: 1;
        }
    }
    &__svg {
        display: inline-block;
        position: relative;
        width: 32px;
        height: 30px;
    }
    &__list {
        position: absolute;
        padding: 0;
        white-space: nowrap;
        border-radius: 4px;
        left: 50%;
        transform: translate(-50%);
        background-color: #fff;
        box-shadow: 0 7px 15px 0 rgba(0,0,0,.12),0 5px 46px 0 rgba(0,0,0,.06);
        z-index: 10;
        margin-top: -8px;
    }
    &__item {
        list-style: none;
        cursor: pointer;
        border-radius: 4px;
        &:hover {
            background-color: #f4f4f4;
        }
    }
}
</style>