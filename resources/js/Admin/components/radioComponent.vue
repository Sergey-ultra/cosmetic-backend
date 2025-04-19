<template>
    <label class="custom-label">
        <input  v-model="currentValue" type="radio" :value="value">
        <span class="custom-radio-button"></span>
        <slot></slot>
    </label>
</template>
<script setup>
import {computed, defineEmits, defineProps } from "vue";

const emit = defineEmits(['update:modelValue']);
const props = defineProps({
    modelValue: {
        type: [String, Number],
    },
    value: {
       type: [ String, Number ]
    },
    color: {
        type: String,
    }
});

let currentValue = computed({
    get() {
        return props.modelValue;
    },
    set(value) {
        emit('update:modelValue', value);
    }
});
</script>
<style scoped lang="scss">
$mainColor: #454cee;
$mainFontColor: #26325c;
$mainGreyColor: #e8ebef;
.custom-label {
    display:inline-flex;
    align-items:center;
    padding-left: 25px;
    position: relative;
    margin: 10px 0;
    cursor: pointer;
    color: $mainFontColor;

    & + .custom-label {
        margin-left: 25px;
    }

    & input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        &:checked + .custom-radio-button {
            background-color: $mainColor;
            border-color: $mainColor;

            &:after {
                content: "";
                display: block;
                width: 8px;
                height: 5px;
                border-bottom: 2px solid #fff;
                border-left: 2px solid #fff;
                position: absolute;
                top: 39%;
                left: 51%;
                transform: translate(-50%, -50%) rotate(-45deg);
            }
        }
    }
}

.custom-radio-button {
    transition: all .3s ease;
    position: absolute;
    top: 0;
    left: 0;
    height: 20px;
    width: 20px;
    background-color: #fff;
    border: 2px solid $mainGreyColor;
    border-radius: 50%;
}
</style>
