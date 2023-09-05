<template>
    <input
        class="input"
        type="text"
        v-model="currentValue"
        :class="{
             [`input--${color}`]: Boolean(color)
        }"
    >
</template>

<script setup>
import { computed, defineEmits, defineProps } from "vue";

const emit = defineEmits(['update:modelValue']);
const props = defineProps({
    modelValue: {
        type: String,
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

<style lang="scss" scoped>
.input {
    width: 100%;
    outline: none;
    overflow: visible;
    transition: background-color 0.3s ease 0s, border-color 0.3s ease 0s;
    border: 1px solid transparent;
    border-radius: 8px;
    height: 35px;
    padding: 4px 10px;
    background-color: rgb(240, 242, 252);

    &:hover {
        border-color: rgb(192, 201, 240);
        transition: border-color 0.3s ease 0s;
    }
    &:focus {
        background-color: white;
        border-color: rgb(59, 87, 208);
        transition: background-color 0.3s ease 0s, border-color 0.3s ease 0s;
    }
    &--white {
        background-color: white;
        width: 100%;
        border-radius: 8px;
        border: 1px solid #d9d9d9;
        color: #333;
        outline: none;

        &:focus {
            border-color: #3b57d0;
            transition: background-color .3s ease 0s, border-color .3s ease 0s;
        }
    }
}
</style>
