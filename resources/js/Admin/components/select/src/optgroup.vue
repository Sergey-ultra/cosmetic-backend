<template>
    <div
        v-if="node && node.type == 'optgroup'"
        class="ej-select__optgroup"
    >
        <div
            class="ej-select__optgroup-label"
            :class="{
                disabled: node.disabled,
            }">
            {{ label }}
        </div>
        <template v-if="node.children && node.children.length">
            <SelectOptgroup
                v-for="(item, index) in node.children"
                :key="index"
                :node="item"
                @click-option="clickChildOption"
            />
        </template>
    </div>
    <div
        v-else-if="node && node.type === 'option' && !isEmptyOption(node)"
        class="ej-select__option"
        :class="{
            active: node.selected,
            disabled: node.disabled,
        }"
        @click.stop="clickOption">
        <div v-if="color">
            <div
                class="ej-select__color"
                :style="{ backgroundColor: getOptionColor(color) }"
            />
        </div>
        <div v-html="label" />
    </div>
</template>

<script>
import { getComponentColorCssValue } from '@ej/components/core';
import { isEmptyOption } from './functions';

export default {
    name: 'SelectOptgroup',
    props: {
        node: {
            type: Object,
            default: null,
        },
    },
    computed: {
        color() {
            return this.node.color || null;
        },
        label() {
            return this.node.label || '&nbsp;';
        },
        title() {
            return this.node.title || '&nbsp;';
        },
    },
    methods: {
        clickOption() {
            this.$emit('click-option', this.node);
        },
        clickChildOption(eventData) {
            this.$emit('click-option', eventData);
        },
        isEmptyOption,
        getOptionColor: getComponentColorCssValue,
    },
};
</script>
