<template>
    <div
        class="select-comp"
        :class="{
          'select--fill': fill,
        }"
    >
        <div
            ref="headerRef"
            class="select__header"
            :class="{
                [`select--${color}`]: Boolean(color),
               'focus': !disabled && (isActive || isFocus),
            }"
            @click.passive="onClick"
        >
            <div
                v-if="isHasValue"
                class="select__label">
                {{ selectedOptions[0].title }}
            </div>
            <div
                v-else
                class="select__placeholder">
                {{ placeholder }}
            </div>
            <fa class="select__triangle" icon="angle-down"></fa>
        </div>

        <template v-if="isDropdownActivated">
            <transition name="fade">
                <div
                    v-show="isActive"
                    ref="popupRef"
                    :style="popupStyle"
                    :class="{
                        'select-popup--left':  alignment.x === 'left',
                        'select-popup--right':  alignment.x === 'right',
                        'select-popup--top': alignment.y === 'top',
                        'select-popup--bottom': alignment.y === 'bottom',
                    }"
                    class="select-popup"
                    @click.passive="onBlur">
                    <div class="select-popup__inner">
                        <slot name="create"></slot>
                        <div
                            v-if="isSearch && !multiple"
                            class="select__input-wrap">
                            <input
                                v-model="searchValueModel"
                                class="select__input"
                                :disabled="disabled"
                                :placeholder="placeholderSearch"
                                @click.stop
                                @keyup.enter.passive="onLookup"
                            />
                        </div>

                        <div
                            ref="scroll"
                            class="select__scroll">
                            <div
                                v-if="isLoading"
                                class="select__loading select__message">
                            </div>
                            <template v-else>
                                <div
                                    class="select__option"
                                    :class="{
                                        'active': option.selected
                                    }"
                                    v-for="(option, itemIndex) in filteredOptions"
                                    :key="itemIndex"
                                    @click="onClickOption(option)"
                                >
                                    {{ option.title }}
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </transition>
        </template>

        <select
            v-show="false"
            :multiple="multiple"
            disabled
        >
        </select>
    </div>
</template>
<script setup>
import { getPopupAlignmentGreedy } from '../lib/get-popup-alignment';
import {computed, nextTick, ref} from "vue";


const emit = defineEmits(['blur', 'select-option', 'deselect-option', 'focus', 'update:modelValue']);

const props = defineProps({
    modelValue: {
        type: [Array, String, Number],
    },
    color: {
        type: String,
    },
    isLoading: {
        type: Boolean,
        default: false,
    },
    placeholder: String,
    placeholderSearch: {
        type: String,
        default: 'Найти',
    },
    disabled : {
        type: Boolean,
        default: false,
    },
    fill: {
        type: Boolean,
        default: false,
    },
    isCreateBlock: {
        type: Boolean,
        default: false,
    },
    /**
     * Разрешить поиск
     */
    isSearch: {
        type: Boolean,
        default: false,
    },
    /**
     * Возможность выбрать несколько опций
     */
    multiple: {
        type: Boolean,
        default: false,
    },
    items: {
        type: Array,
        default: () => [],
    },
    itemTitle: {
        type:String,
        default: null
    },
    itemValue: {
        type:String,
        default: null
    },

});

let alignment = ref({
    x: 'left',
    y: 'bottom',
});
let popupRef = ref(null);
let headerRef = ref(null);
let popupStyle = ref({});
let isDropdownActivated = ref(false);
let isActive = ref(false); // открыт дропдаун
let isFocus = ref(false); // поле ввода в фокусе
let searchValueModel = ref('');

let preparedOptions = computed(() => {
    return props.items.map(option => {
        let obj = {};
        if (typeof option === 'object' && props.itemTitle && props.itemValue) {
            obj = {
                title: option[props.itemTitle],
                value: option[props.itemValue],
            };
        } else {
            obj = {
                title: option,
                value: option,
            };
        }
        obj.selected = Array.isArray(props.modelValue)
            ? props.modelValue.includes(obj.value)
            : String(props.modelValue) === String(obj.value);
        return obj;
    })
});

let filteredOptions = computed(() => {
    if (searchValueModel.value !== '') {
        return preparedOptions.value.filter(el => el.title.includes(searchValueModel.value));
    }

    return preparedOptions.value;
});

let selectedOptions = computed(() => preparedOptions.value.filter(el => el.selected));

let isHasValue = computed(() => selectedOptions.value && selectedOptions.value.length);

let selectedOptionValues = computed(() => selectedOptions.value.map(node => node.value));


let currentValue = computed({
    get() {
        return props.modelValue;
    },
    set(value) {
        emit('update:modelValue', value);
    }
});

const onClick = (event) => {
    if (isActive.value) {
        hide();
    } else {
        show(event);
    }
};

const hide = () => {
    // if (this.$refs.searchInput) {
    //     this.$refs.searchInput.blur();
    // }

    isActive.value = false;
    isFocus.value = false;

    searchValueModel.value = '';
     //searchValue.value = '';
};

// const onFocusInput = () => {
//     onFocus();
//     show();
// };

const onFocus = () => {
    if (props.disabled || (isActive.value && isFocus.value)) {
        return;
    }

    emit('focus');
};

const closePopUp = event => {
    if (!popupRef.value.contains(event.target)) {
       hide();
    }
};
const show = (event) => {
    event.stopPropagation();
    if (!isDropdownActivated.value) {
        isDropdownActivated.value = true;
    }
    document.addEventListener('click', closePopUp, { once: true });
    isActive.value = true;
    isFocus.value = true;

    definePopupAlignment();
};

const definePopupAlignment = () => {
    // if ($media.mobile) {
    //     scrollToTop();
    //     return;
    // }

    nextTick(() => {
        popupStyle.value = {};
        nextTick(() => {
            let alignmentValues = getPopupAlignmentGreedy(headerRef.value, popupRef.value);
            alignment.value = alignmentValues;
            popupStyle.value = {
                maxHeight: `${alignmentValues.yMax}px`,
                maxWidth: `${alignmentValues.xMax}px`,
            };

            scrollToTop();
        });
    });
};

const onLookup = () => {
    // if (this.disabled) {
    //     return;
    // }
    //
    // if (this.searchValue !== this.searchValueModel) {
    //     this.searchValue = this.searchValueModel;
    //
    //     if (this.isFetch) {
    //         this.fetchOptions();
    //     }
    // }
};

const scrollToTop = () => {
    // if (this.$refs.scroll) {
    //     this.$refs.scroll.scrollTop = 0;
    // }
    // if (this.$refs.searchInput) {
    //     this.$nextTick(() => {
    //         this.$refs.searchInput.focus();
    //     });
    // }
};

const onBlur = () => {
    if (props.disabled || (!isActive.value && !isFocus.value)) {
        return;
    }

    hide();
    emit('blur');
};
const onClickOption = element => {
    if (props.multiple) {
        searchValueModel.value = '';
    } else {
        isActive.value = !isActive.value;
    }

    if (!element.selected) {
        if (Array.isArray(currentValue.value)) {
            currentValue.value.push(element.value);
        } else {
            currentValue.value = element.value;
        }

        emit('select-option', { option: element, value: selectedOptionValues.value });
    } else if (element.selected  && props.multiple) {
        let index = currentValue.value.indexOf(element.value);

        if (index !== -1) {
            currentValue.value.splice(index, 1);
        }
        emit('deselect-option', { option: element, value: selectedOptionValues.value });
    }
};
</script>

<style scoped lang="scss">
.select {
    &-comp {
        outline: none;
        position: relative;
        width: 100%;
        overflow: visible;
        transition: background-color 0.3s ease 0s, border-color 0.3s ease 0s;

    }
    &--fill {
        max-width: none;
        width: 100%;
    }
    &__header {
        padding: 4px 10px;
        font-size: 15px;
        text-align: left;
        display: grid;
        grid-template-columns: 1fr 22px;
        grid-template-rows: 1fr;
        grid-gap: 0 0;
        background-color: rgb(240, 242, 252);
        color: #242322;
        border-radius: 8px;
        border: 1px solid transparent;
        line-height: 27px;
        height: 35px;
        transition: background-color 0.3s ease 0s, border-color 0.3s ease 0s;

        &.focus {
            background-color: white;
            border-color: #3b57d0;
            outline: none;
            &:hover {
                border-color: #3b57d0;
            }
        }
        &:hover {
            border-color: rgb(192, 201, 240);
        }
    }
    &__triangle {
        line-height: 22px;
        height: 22px;
        overflow: hidden;
        width: 22px;
    }
    &-popup {
        display: flex;
        position: absolute;
        height: auto;
        min-width: 100%;
        text-align: left;
        text-shadow: none;
        z-index: 200;
        &__inner {
            width: 100%;
            display: flex;
            flex-direction: column;
            min-height: 0;
            min-width: 0;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            color: #242322;
            font-size: 14px;
            line-height: 20px;
        }

        &--bottom {
            top: 100%;
        }

        &--top {
            bottom: 100%;
        }

        &--left {
            left: 0;
        }

        &--right {
            right: 0;
        }
    }
    &__input {
        background-color: #fff;
        border-width: 1px;
        border-style: solid;
        border-color: #ccc;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) inset;
        color: #242322;
        cursor: text;
        font-family: inherit;
        font-size: 15px;
        font-weight: normal;
        line-height: 22px;
        height: 35px;
        padding: 4px 4px;
        text-align: left;
        vertical-align: middle;
        appearance: none;
        display: block;
        width: 100%;
        overflow: hidden;
        outline: none;
        text-overflow: ellipsis;
        white-space: nowrap;
        &-wrap {
            flex: none;
            margin: 8px;

            & .select__scroll {
                border-top: 1px solid #ddd;
            }
        }
    }
    &__scroll {
        flex: 0 1 auto;
        display: block;
        overflow-x: hidden;
        overflow-y: auto;
        scrollbar-color: #bcbbb9 transparent;
        scrollbar-width: thin;
    }
    &__option {
        border: 0;
        cursor: pointer;
        display: flex;
        padding: 5px 8px;
        font-size: 15px;
        font-style: normal;
        font-weight: 400;
        line-height: 22px;
        min-width: 24ch;
        white-space: normal;
        user-select: none;
        &.active {
            background-color: rgba(12, 119, 195, 0.92);
            color: #fff;
            font-weight: 450;
        }
        &:hover {
            background-color: rgba(12, 119, 195, 0.1) !important;
            color: #242322;
        }
    }

    &--white {
        border: 1px solid #d9d9d9;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) inset;
        background-color: white;
        width: 100%;
        color: #333;
        outline: none;

        //&:focus {
        //    border-color: #3b57d0;
        //    transition: background-color .3s ease 0s, border-color .3s ease 0s;
        //}
    }
}
</style>
