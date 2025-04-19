<template>
    <div class="rating">
        <span
                class="rating__item"
                :class="{'rating__item-selected': (value <= selectedRating && enteredValue === 0) ||  value <= enteredValue}"
                v-for="value in [1,2,3,4,5]"
                :key="value"
                @mouseenter="enteredValue = value"
                @mouseleave="enteredValue = 0"
                @click="setRating(value)"
        >
            <svg width="28" height="28" viewBox="0 0 28 28" version="1.1"
                 xmlns="http://www.w3.org/2000/svg">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <path d="M11,17.1483333 L16.1810004,20.2753773 C16.6538365,20.5607621 17.268496,20.4088029 17.5538808,19.9359669 C17.6885693,19.7128101 17.7308035,19.4458833 17.6715868,19.1920461 L16.2966667,13.2983333 L20.8752723,9.3318856 C21.2927035,8.9702648 21.3379468,8.3387183 20.9763259,7.9212871 C20.8056446,7.7242638 20.5647759,7.6016755 20.3050362,7.5796391 L14.2783333,7.0683333 L11.9207179,1.50570331 C11.7052005,0.99720486 11.1182696,0.75969683 10.6097711,0.97521427 C10.3707977,1.07649865 10.1805665,1.26672985 10.0792821,1.50570331 L7.7216667,7.0683333 L1.69496379,7.5796391 C1.144656,7.6263273 0.73639162,8.1102881 0.78307976,8.6605959 C0.80511609,8.9203356 0.92770441,9.1612042 1.12472773,9.3318856 L5.70333333,13.2983333 L4.32841315,19.1920461 C4.20294189,19.7298893 4.53723546,20.267612 5.07507876,20.3930833 C5.32891596,20.4523 5.5958428,20.4100658 5.81899958,20.2753773 L11,17.1483333 Z"
                          fill="#000000"></path>
                </g>
            </svg>
        </span>
        <div class="rating__text">{{ ratingText(selectedRating) }}</div>
    </div>
</template>

<script setup>
import { computed, ref, defineEmits, defineProps } from "vue";

const emit = defineEmits(['update:modelValue']);

const props = defineProps({
    modelValue: {
        type: Number,
        default: 0,
    },
});

const enteredValue = ref(0);

let selectedRating = computed({
    get() {
        return props.modelValue;
    },
    set(value) {
        emit('update:modelValue', value);
    }
});

const setRating = value => selectedRating.value = value;

const ratingText = value => {
    switch (Number(value)) {
        case 5:
            return 'Отличный товар';
        case 4:
            return 'Хороший товар';
        case 3:
            return 'Обычный товар';
        case 2:
            return 'Плохой товар';
        case 1:
            return 'Ужасный товар';
        default:
            return '';
    }
};

</script>

<style lang="scss" scoped>
    .rating {
        display: flex;
        align-items: center;
        user-select: none;
        color: #878787;
        &__item {
            height: 36px;
            width: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 0;

            & svg {
                height:28px;
                width: 28px;
                & path {
                    fill: #d9d9d9;
                }
            }
            &-selected svg path {
                fill: #ffda66;
            }
        }
    }
</style>
