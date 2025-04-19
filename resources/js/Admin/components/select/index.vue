<template>
    <div
        v-click-outside="onBlur"
        class="ej-select"
        :class="{
            'ej-select--fill': fillWidth,
            'ej-select--inline': inline,
        }">
        <div
            ref="header"
            class="ej-select__header"
            :class="{
                'ej-select__header--small': size === 'small',
                'ej-select__header--big': size === 'big',

                'ej-select__header--multiple': multiple,
                'ej-select__header--search': isSearch,

                'active': isActive,
                'disabled': disabled,
                'focus': !disabled && (isActive || isFocus),
                'invalid': invalid || $formlyInvalid,
            }"
            :style="headerStyle"
            tabindex="-1"
            @click.passive="onClick"
            @focus.passive="onFocus"

            @keyup.enter.passive="onKeyupEnter"
        >
            <div
                v-if="multiple"
                class="ej-select__tags">
                <slot name="custom-format">
                    <TagComponent
                        v-for="(item, index) in optionsSelected"
                        :key="`tag-${index}`"
                        class="ej-select__tag"
                        :label="item.label"
                        :title="item.label"
                        :disabled="disabled"
                        :removable="true"
                        :size="size"
                        hoverable
                        :auto-width="autoWidthOptions"
                        @close="deselectOption(item)" />
                </slot>

                <input
                    v-if="isSearch"
                    ref="searchInput"
                    v-model="searchValueModel"
                    class="ej-select__tags-input"
                    :disabled="disabled"
                    :placeholder="placeholderValueSearch"
                    @click.stop
                    @focus.passive="onFocusInput"
                    @keyup.enter.passive="onLookup"
                />

                <div
                    v-show="!isSearch && !hasValue"
                    class="ej-select__placeholder">
                    {{ placeholderValueSelect }}
                </div>
            </div>

            <template v-else>
                <div
                    v-show="hasValue"
                    :style="{ color: getValueColor(optionSelectedColor) }"
                    class="ej-select__label">
                    {{ optionsSelectedLabels[0] }}
                </div>
                <div
                    v-show="!hasValue"
                    class="ej-select__placeholder">
                    {{ placeholderValueSelect }}
                </div>
            </template>

            <div class="ej-select__triangle"></div>
        </div>

        <template v-if="isDropdownActivated">
            <transition name="fade">
                <div
                    v-show="isActive"
                    ref="popup"
                    :style="popupStyle"
                    :class="{
                        'ej-select-popup': true,
                        'ej-select-popup--left': $media.desktop && alignment.x === 'left',
                        'ej-select-popup--right': $media.desktop && alignment.x === 'right',
                        'ej-select-popup--top': $media.desktop && alignment.y === 'top',
                        'ej-select-popup--bottom': $media.desktop && alignment.y === 'bottom',
                    }"
                    @click.passive="onBlur">
                    <div class="ej-select-popup__inner">
                        <div
                            v-if="isSearch && !multiple"
                            class="ej-select__input-wrap">
                            <input
                                ref="searchInput"
                                v-model="searchValueModel"
                                class="ej-select__input"
                                :disabled="disabled"
                                :placeholder="placeholderValueSearch"
                                @keyup.enter.passive="onLookup"
                            />
                        </div>

                        <div
                            ref="scroll"
                            class="ej-select__scroll">
                            <div
                                v-if="isLoading"
                                class="ej-select__loading ej-select__message">
                                {{ $t("action.loading") }}...
                            </div>
                            <div
                                v-else-if="isNotFound"
                                class="ej-select__message">
                                {{ $t("action.not_found") }}
                            </div>
                            <div
                                v-else-if="isEmpty && !isFetch"
                                class="ej-select__message">
                                {{ $t("components.select.empty") }}
                            </div>
                            <template v-else>
                                <SelectOptgroup
                                    v-for="(item, itemIndex) in optionsFiltered"
                                    :key="itemIndex"
                                    :node="item"
                                    @click-option="onClickOption"
                                />
                                <div v-if="!isSearchEnabled && isFetch">
                                    <div class="ej-select__message">
                                        {{ searchMorePhrase }}
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </transition>
        </template>
        <!-- </template> -->

        <select
            v-show="false"
            ref="optionsContainer"
            :multiple="multiple"
            disabled>
            <slot />
        </select>
    </div>
</template>

<script>
import debounce from 'lodash/debounce';
import isEqual from 'lodash/isEqual';
import { getPopupAlignmentGreedy } from '@ej/functions/interface/get-popup-alignment';

import { COMPONENT_SIZES, componentSizeValidator, getComponentColorCssValue } from '@ej/components/core';
import TagComponent from '@ej/components/tag';
import SelectOptgroup from './src/optgroup.vue';

import {
    createOptions, createOptionsFromNodes,
    filterOptions, selectedOptions,
    setOptionSelected, setupOptionSelected,
    isEmptyOption,
    valueToString,
} from './src/functions';

export default {
    components: {
        SelectOptgroup,
        TagComponent,
    },
    inject: { errors: { default: {} } },
    props: {
        disabled: {
            type: Boolean,
            default: false,
        },
        /**
         * Позволяет заполнять всю ширину контейнера
         */
        fill: {
            type: Boolean,
            default: false,
        },
        /**
         * URL для запроса опций
         */
        fetch: {
            type: String,
            default: null,
        },
        /**
         * Заставляет вести себя как строчный элемент
         */
        inline: {
            type: Boolean,
            default: false,
        },
        /**
         * Неверное значение поля
         */
        invalid: {
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

        /**
         * Массив опций
         */
        options: {
            type: [Array, String],
            default() {
                return [];
            },
        },

        placeholder: {
            type: String,
            default: 'Выберите значение',
        },
        placeholderSearch: {
            type: String,
            default: 'Найти',
        },

        searchMorePhrase: {
            type: String,
            default: 'Воспользуйтесь поиском, чтобы получить дополнительные результаты',
        },

        /**
         * Переход по ссылке при изменении значения
         */
        redirect: {
            type: Boolean,
            default: false,
        },

        /**
         * Разрешить поиск
         */
        search: {
            type: Boolean,
            default: false,
        },
        /**
         * Минимальное число знаков для поиска
         */
        searchThreshold: {
            type: Number,
            default: 2,
        },
        /**
         * Размер компонента `small | medium | big`
         * @default `medium`
         */
        size: {
            type: String,
            default: COMPONENT_SIZES.MEDIUM,
            validator: componentSizeValidator,
        },
        value: {}, // eslint-disable-line vue/require-default-prop, vue/require-prop-types
        width: {
            type: String,
            default: null,
        },

        // опции в multiple режиме заполняют всю доступную им ширину
        autoWidthOptions: {
            type: Boolean,
            default: false,
        },
        /**
         * Маппер при fetch-запросе
         */
        fetchMapper: {
            type: Function,
            default: null,
        },
        /**
         * Ключ, где лежат данные при fetch-запросе
         */
        fetchKey: {
            type: String,
            default: 'options',
        },
    },
    data() {
        return {
            alignment: {
                x: 'left',
                y: 'bottom',
            },

            isActive: false, // открыт дропдаун
            isFocus: false, // поле ввода в фокусе
            isLoading: false,
            isReady: false,

            isDropdownActivated: false,

            optionsInternal: [], // все локальные опции

            optionsFetch: [], // все опции с сервера
            optionsFetchSelected: [], // все выбранные опции с сервера

            placeholderValueSearch: this.placeholderSearch
                || this.$t('components.select.find'),
            placeholderValueSelect: this.placeholder
                || this.$t('components.select.choose'),

            popupStyle: {},

            searchValue: '',
            searchValueModel: '',

        };
    },
    computed: {
        debounceInput() {
            return debounce(() => {
                this.onLookup();
            }, 200);
        },
        isFetch() {
            return this.fetch !== null;
        },
        isNotFound() {
            return this.isSearchEnabled
                && (!this.optionsFiltered || this.optionsFiltered.length === 0);
        },
        isEmpty() {
            return !this.isSearchEnabled
                && (!this.optionsFiltered || this.optionsFiltered.length === 0);
        },

        isSearch() {
            return this.search === true || this.isFetch;
        },
        isSearchEnabled() {
            return (this.isFetch || this.optionsInternal)
                && this.searchValue && this.searchValue.length >= this.searchThreshold;
        },

        fillWidth() {
            return this.width || this.fill;
        },

        hasSelectIdInProp() {
            return this.options && this.options.length && typeof this.options === 'string';
        },
        hasOptionsInProp() {
            return this.options && this.options.length && Array.isArray(this.options);
        },

        hasValue() {
            return this.optionsSelected && this.optionsSelected.length;
        },

        optionsFiltered() { // вычищенные опции
            if (this.isFetch) {
                return this.isSearchEnabled
                    ? this.optionsFetch
                    : this.optionsInternal;
            }

            return this.isSearchEnabled
                ? filterOptions(this.optionsInternal, this.searchValue)
                : this.optionsInternal;
        },

        // выбранные опции
        optionsSelected() {
            if (this.isFetch) {
                return this.optionsFetchSelected;
            }

            let selected = (this.optionsInternal && this.optionsInternal.length)
                ? selectedOptions(this.optionsInternal) : [];

            if (selected.length === 1 && isEmptyOption(selected[0])) {
                return [];
            }

            return selected;
        },

        optionsSelectedValues() {
            return (this.optionsSelected && this.optionsSelected.length)
                ? this.optionsSelected.map((node) => node.value) : [];
        },
        optionsSelectedLabels() {
            return (this.optionsSelected && this.optionsSelected.length)
                ? this.optionsSelected.map((node) => node.label) : [];
        },

        optionSelectedColor() {
            return (this.optionsSelected && this.optionsSelected.length)
                ? this.optionsSelected[0].color : null;
        },
        headerStyle() {
            if (this.multiple) {
                return '';
            }

            let length = 5;
            if (this.optionsSelectedLabels
                && this.optionsSelectedLabels.length) {
                let callback = (item) => Math.max(
                    item && item.label ? item.label.length : 1,
                    item && item.title ? item.title.length : 1,
                );
                length = this.optionsSelectedLabels
                    ? Math.max(...this.optionsSelectedLabels.map(callback)) + 2.5
                    : 5;

                length = Math.max(length, 5);
            }

            return `min-width:${length}ch`;
        },


        valueInput() {
            // console.log(this._uid, "valueInput before:", this.value, typeof this.value);
            if ((this.value || this.value == 0) // eslint-disable-line eqeqeq
                && this.multiple
                && typeof this.value === 'string') {
                let value = this.value.toString();

                try {
                    value = JSON.parse(value);
                } catch (e) {
                    value = this.value.toString();
                }

                return value;
            }

            return this.value;
        },
        valueInputString() {
            return valueToString(this.valueInput);
        },

        valueOutput() {
            if (!this.multiple && this.optionsSelectedValues[0] === 0) {
                return this.optionsSelectedValues[0];
            }
            return (
                this.multiple ? this.optionsSelectedValues : this.optionsSelectedValues[0]
            ) || null;
        },
        valueOutputString() {
            return valueToString(this.valueOutput);
        },
    },

    watch: {
        options() {
            if (this.hasOptionsInProp) {
                this.optionsInternal = createOptions(this.options, this.valueInput);
            }
        },
        optionsSelectedValues(newValue, oldValue) {
            if (!this.isReady) {
                return;
            }

            let newValueToString = valueToString(newValue);
            let oldValueToString = valueToString(oldValue);

            if (this.valueInputString !== this.valueOutputString
                && newValueToString !== oldValueToString
            ) {
                // && this.valueOutput !== null) { // вернуть, если что-то сломается
                this.$emit('input', this.valueOutput);
                this.$emit('change', this.valueOutput);

                if (// this.valueInput !== undefined
                    // && this.valueInput !== null
                    this.redirect
                    && !this.multiple
                    && this.valueOutput) {
                    let href = String(this.valueOutput);
                    if (href && href !== window.location.href) {
                        window.location.href = href;
                        if (window.showLoadingScreen) {
                            window.showLoadingScreen();
                        }
                    }
                }
            } else if (typeof oldValue === 'object'
                && typeof newValue === 'object'
                && !isEqual(oldValue, newValue)
            ) {
                this.$emit('input', this.valueOutput);
                this.$emit('change', this.valueOutput);
            }
        },
        searchValueModel(newValue, oldValue) {
            if (newValue !== oldValue
                && (oldValue.length >= this.searchThreshold
                    || newValue.length >= this.searchThreshold)) {
                this.debounceInput();
                this.$emit('search', newValue);
            }
        },
        value() {
            if (this.valueInputString !== this.valueOutputString) {
                setupOptionSelected(
                    this.optionsInternal, this.valueInput, this.multiple,
                );
            }
        },
    },
    // beforeMount() {
    //     let { customElement } = this.$root.$options;
    //     if (customElement
    //         // && customElement.attributes
    //         && customElement.attributes.tabindex === undefined) {
    //         customElement.attributes.tabindex = -1;
    //     }
    // },
    mounted() {
        let slotMutationCallback = () => {
            if (!this.hasOptionsInProp) {
                this.collectOptions();
            }
        };

        let elementToObserve;
        if (this.hasSelectIdInProp) {
            elementToObserve = document.getElementById(this.options);
        } else if (!this.hasOptionsInProp
            && this.$refs.optionsContainer
            && this.$refs.optionsContainer.childElementCount) {
            elementToObserve = this.$refs.optionsContainer;
        }

        if (elementToObserve) {
            this.slotObserver = new MutationObserver(slotMutationCallback.bind(this));
            this.slotObserver.observe(
                elementToObserve,
                {
                    childList: true,
                    subtree: true,
                },
            );
        }

        this.collectOptions();
    },
    beforeDestroy() {
        if (this.slotObserver && this.slotObserver.disconnect) {
            this.slotObserver.disconnect();
        }
    },

    methods: {
        blur() {
            this.onBlur();
        },
        focus() {
            this.$refs.header.scrollIntoView();
            this.$refs.header.focus();
        },

        collectOptions() {
            if (this.hasOptionsInProp) {
                this.optionsInternal = createOptions(
                    this.options, this.valueInput, this.multiple,
                );
            } else if (this.hasSelectIdInProp) {
                let element = document.getElementById(this.options);
                if (!element) {
                    setTimeout(this.collectOptions, 500);
                    if (this.isFetch) {
                        this.makeReady();
                    }
                    return;
                }
                this.optionsInternal = createOptionsFromNodes(
                    element.children, this.valueInput, this.multiple,
                );
            } else if (this.$refs.optionsContainer) {
                if (!this.$refs.optionsContainer.childElementCount) {
                    setTimeout(this.collectOptions, 500);
                    if (this.isFetch) {
                        this.makeReady();
                    }
                    return;
                }
                this.optionsInternal = createOptionsFromNodes(
                    this.$refs.optionsContainer.children, this.valueInput, this.multiple,
                );
            }

            this.makeReady();
        },

        makeReady() {
            if (!this.isReady) {
                this.$nextTick(() => {
                    this.isReady = true;
                });
            }
        },

        fetchOptions() {
            if (!this.isSearchEnabled) {
                this.optionsFetch = this.optionsInternal;
                this.isLoading = false;

                return;
            }

            let url = new URL(this.fetch);
            let params = {
                value: this.searchValue,
            };

            Object.keys(params)
                .forEach((key) => url.searchParams.append(key, params[key]));

            this.isLoading = true;
            fetch(url, {
                mode: 'cors',
            }).then((response) => {
                if (response.ok) {
                    return response.json();
                }
                return Promise.reject();
            }).then((json) => {
                // console.log(this._uid, json);
                if (this.fetchMapper && typeof this.fetchMapper === 'function') {
                    json[this.fetchKey] = json[this.fetchKey].map(this.fetchMapper);
                }
                this.optionsFetch = createOptions(json[this.fetchKey], this.value, this.multiple);
                this.isLoading = false;
            }, () => {
                this.optionsFetch = [];
                this.isLoading = false;
            }).catch((err) => {
                this.optionsFetch = [];
                this.isLoading = false;
            });
        },

        hide() {
            if (this.$refs.searchInput) {
                this.$refs.searchInput.blur();
            }

            this.isActive = false;
            this.isFocus = false;

            this.searchValueModel = '';
            this.searchValue = '';
        },
        show() {
            if (!this.isDropdownActivated) {
                this.isDropdownActivated = true;
            }

            this.isActive = true;
            this.isFocus = true;

            this.definePopupAlignment();
        },

        onBlur() {
            if (this.disabled || (!this.isActive && !this.isFocus)) {
                return;
            }

            this.hide();
            this.$emit('blur');
        },

        onClick() {
            if (!this || this.disabled || !this.hide || !this.show) {
                return;
            }

            if (this.isActive) {
                this.hide();
            } else {
                this.show();
            }
        },
        onKeyupEnter() {
            if (this.disabled) {
                return;
            }

            this.hide();
        },
        onFocus() {
            if (this.disabled || (this.isActive && this.isFocus)) {
                return;
            }

            // this.show();
            this.$emit('focus');
        },
        onFocusInput() {
            this.onFocus();
            this.show();
        },
        onLookup() {
            if (this.disabled) {
                return;
            }

            if (this.searchValue !== this.searchValueModel) {
                this.searchValue = this.searchValueModel;

                if (this.isFetch) {
                    this.fetchOptions();
                }
            }
        },
        onClickOption(element) {
            if (element.type === 'optgroup' || element.disabled) {
                return;
            }

            if (this.multiple) {
                this.searchValueModel = '';
            }

            if (!this.multiple) {
                this.isActive = !this.isActive;
            }

            if (element.selected !== true) {
                this.selectOption(element);
            } else if (element.selected === true && this.multiple) {
                this.deselectOption(element);
            }
        },

        selectOption(element) {
            let { value } = element;

            if (this.optionsSelectedValues.length === 0
                || this.optionsSelectedValues.indexOf(value) === -1) {
                if (this.isFetch) {
                    if (this.multiple) {
                        this.optionsFetchSelected.push(element);
                    } else {
                        this.optionsFetchSelected = [element];
                    }
                    return;
                }

                if (this.multiple && this.optionsSelectedValues.length > 0) {
                    value = this.optionsSelectedValues.concat([value]);
                }

                setOptionSelected(
                    this.optionsInternal, value, true, this.multiple,
                );

                this.$emit('select-option', { option: element, value: this.optionsSelectedValues });
            }
        },
        deselectOption(element) {
            let { value } = element;
            let index = this.optionsSelectedValues.indexOf(value);

            if (index !== -1) {
                if (this.isFetch) {
                    if (this.multiple) {
                        this.optionsFetchSelected.splice(index, 1);
                    } else {
                        this.optionsFetchSelected = [];
                    }
                    return;
                }

                setOptionSelected(
                    this.optionsInternal, value, false, this.multiple,
                );

                this.$emit('deselect-option', { option: element, value: this.optionsSelectedValues });
            }
        },

        scrollToTop() {
            if (this.$refs.scroll) {
                this.$refs.scroll.scrollTop = 0;
            }
            if (this.$refs.searchInput) {
                this.$nextTick(() => {
                    // this.$refs.searchInput.scrollIntoView();
                    this.$refs.searchInput.focus();
                });
            }
        },

        definePopupAlignment() {
            if (this.$media.mobile) {
                this.scrollToTop();
                return;
            }

            this.$nextTick(() => {
                this.popupStyle = {};
                this.$nextTick(() => {
                    let alignment = getPopupAlignmentGreedy(this.$refs.header, this.$refs.popup);
                    this.alignment = alignment;
                    this.popupStyle = {
                        maxHeight: `${alignment.yMax}px`,
                        maxWidth: `${alignment.xMax}px`,
                    };

                    this.scrollToTop();
                });
            });
        },

        getValueColor: getComponentColorCssValue,
    },
};
</script>

<style lang="less">
@import "~@less/webcomponents/select.less";
</style>
