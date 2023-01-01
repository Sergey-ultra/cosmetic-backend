<template>
    <div  class="published">

        <slot name="status"></slot>

        <div class="published__title">
            <slot name="title"></slot>
            <div class="dropdown">
                <drop-menu :items="dropMenuItems">
                    <template v-slot:edit>
                        <div class="dropdown__inner">
                            <button class="dropdown__value" @click="$emit('showEditForm')">
                                <span class="dropdown__icon">
                                    <svg>
                                        <use xlink:href="#icons_edit">
                                             <symbol viewBox="0 0 24 24" id="icons_edit">
                                                <path fill-rule="evenodd" d="M18 2l4 4-2 1.999L16 4l2-2zM3 17L14.655 5.343l4 4.001L7.001 21 2 22l1-5z"></path>
                                            </symbol>
                                        </use>
                                    </svg>
                                </span>
                                <span>Изменить</span>
                            </button>
                        </div>
                    </template>
                    <template v-slot:delete>
                        <div class="dropdown__inner">
                            <button class="dropdown__value" @click="$emit('showDeleteForm')">
                                <span class="dropdown__icon">
                                    <svg>
                                        <use xlink:href="#icons_cross">
                                            <symbol viewBox="0 0 24 24" id="icons_cross"><path fill-rule="evenodd" d="M18.997 6.417l-1.414-1.414L12 10.586 6.417 5.003 5.003 6.417 10.586 12l-5.583 5.583 1.414 1.414L12 13.414l5.583 5.583 1.414-1.414L13.414 12z"></path></symbol>
                                        </use>
                                    </svg>
                                </span>
                                <span>Удалить</span>
                            </button>
                        </div>
                    </template>
                </drop-menu>
            </div>
        </div>
        <slot></slot>

        <slot name="bottom"></slot>
    </div>
</template>

<script setup>
    import dropMenu from '../drop-menu'
    import ratingView from "../ratingView";


    const dropMenuItems = ['edit', 'delete'];
</script>

<style scoped lang="scss">
    a {
        text-decoration: none;
    }
    .published {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.16);
        width: 100%;
        margin-bottom: 15px;
        padding: 15px;
        &__status {
            margin: -16px -16px 16px;
            padding: 14px 16px 14px 20px;
            background-color: #ffe1e1;
            font-size: 15px;
            line-height: 18px;
            & span {
                display: inline;
                color: #c70000;
                margin: 8px;
            }
        }
        &__title {
            display: flex;
            align-items: center;
            padding: 0 0 16px;
            border-bottom: 1px solid #e8e8e8;
            margin-bottom: 20px;
        }
    }
    .dropdown {
        margin: -5px 5px -5px auto;
        &__inner {
            font-size: 14px;
            line-height: 16px;
        }
        &__value {
            display: flex;
            align-items: center;
            margin: 0;
            border: none;
            outline: none;
            outline-offset: 2px;
            background: transparent;
            width: 100%;
            padding: 0 12px 0 16px;
            color: #222;
            cursor: pointer;
            user-select: none;
            text-align: left;
            &:hover .span {
                color: #c70000;
            }
            & span {
                font-style: normal;
                font-stretch: normal;
                line-height: 36px;
                white-space: nowrap;
            }
        }
        &__icon {
            display: inline-block;
            position: relative;
            width: 32px;
            height: 32px;
            & svg {
                fill: rgba(0,0,0,.4);
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%,-50%);
                width: 20px;
                height: 20px;
            }
        }
    }
</style>