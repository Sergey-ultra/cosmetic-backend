<!--<template>-->
<!--    <div class="slider">-->
<!--        <div class="slider__line">-->
<!--            <div class="slider__progress" :style="{left: minLeftPosition + '%', right: maxRightPosition + '%'}">-->
<!--            </div>-->
<!--        </div>-->
<!--        <div class="slider__input">-->
<!--            <input-->
<!--                type="range"-->
<!--                class="slider__min"-->
<!--                :min="filters.min_price"-->
<!--                :max="filters.max_price"-->
<!--                v-model="minPriceLocal"-->
<!--                @input="$emit('setMinAndMaxPrices','min')"-->
<!--                @change="$emit('showFilterSubmit', $event)"-->
<!--            >-->
<!--        </div>-->
<!--        <div class="slider__input">-->
<!--            <input-->
<!--                type="range"-->
<!--                class="slider__max"-->
<!--                :min="filters.min_price"-->
<!--                :max="filters.max_price"-->
<!--                v-model="maxPriceLocal"-->
<!--                @input="$emit('setMinAndMaxPrices','max')"-->
<!--                @change="$emit('showFilterSubmit', $event)"-->
<!--            >-->
<!--        </div>-->
<!--    </div>-->
<!--</template>-->

<!--<script>-->

<!--import {useFilterStore} from "../../store/filter";-->
<!--import {mapState} from "pinia";-->

<!--export default {-->
<!--    name: "price-range",-->
<!--    props: {-->
<!--        minPrice: {-->
<!--            default: ''-->
<!--        },-->
<!--        maxPrice: {-->
<!--            default: ''-->
<!--        }-->
<!--    },-->
<!--    computed: {-->
<!--        ...mapState(useFilterStore, ['isLoadingFilter', 'filters']),-->
<!--        minPriceLocal: {-->
<!--            get() {-->
<!--                console.log(this.minPrice)-->
<!--                return this.minPrice-->
<!--            },-->
<!--            set(value) {-->
<!--                this.$emit('update:minPrice', value)-->
<!--            }-->
<!--        },-->
<!--        maxPriceLocal: {-->
<!--            get() {-->
<!--                console.log(this.maxPrice)-->
<!--                return this.maxPrice-->
<!--            },-->
<!--            set(value) {-->
<!--                this.$emit('update:maxPrice', value)-->
<!--            }-->
<!--        },-->
<!--        minLeftPosition() {-->
<!--            if (![null, undefined, ''].includes(this.minPrice) && this.filters.min_price) {-->
<!--                return ((this.minPrice - this.filters.min_price) / (this.filters.max_price - this.filters.min_price)) * 100-->
<!--            }-->
<!--            return 0;-->
<!--        },-->
<!--        maxRightPosition() {-->
<!--            if (![null, undefined, ''].includes(this.maxPrice) && this.filters.max_price) {-->
<!--                return 100 - ((this.maxPrice - this.filters.min_price)/ (this.filters.max_price - this.filters.min_price)) * 100-->
<!--            }-->
<!--            return 0;-->
<!--        },-->
<!--    }-->
<!--}-->
<!--</script>-->

<!--<style scoped lang="scss">-->
<!--    .slider {-->
<!--        margin: 20px 15px 0;-->

<!--        &__line {-->
<!--            position: relative;-->
<!--            border-radius: 5px;-->
<!--            height: 5px;-->
<!--            background-color: #ddd;-->
<!--        }-->
<!--        &__progress {-->
<!--            position: absolute;-->
<!--            height: 5px;-->
<!--            border-radius: 5px;-->
<!--            background-color: #46bd87;-->
<!--        }-->
<!--        &__input {-->
<!--            position: relative;-->
<!--            & input {-->
<!--                position: absolute;-->
<!--                top: -5px;-->
<!--                height: 5px;-->
<!--                width: 100%;-->
<!--                background: none;-->
<!--                -webkit-appearance: none;-->
<!--                pointer-events: none;-->
<!--            }-->
<!--        }-->
<!--        input[type="range"]::-webkit-slider-thumb {-->
<!--            height: 17px;-->
<!--            width: 17px;-->
<!--            pointer-events: auto;-->
<!--            -webkit-appearance: none;-->
<!--            background: #46bd87;-->
<!--            border-radius: 50%;-->
<!--        }-->
<!--        input[type="range"]::-moz-range-thumb {-->
<!--            height: 17px;-->
<!--            width: 17px;-->
<!--            pointer-events: auto;-->
<!--            border: none;-->
<!--            -moz-appearance: none;-->
<!--            background: #42b983;-->
<!--            border-radius: 50%;-->
<!--        }-->
<!--    }-->


<!--</style>-->