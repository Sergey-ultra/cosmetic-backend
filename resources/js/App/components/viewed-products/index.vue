<template>
    <div class="viewed-products" v-if="viewedSkuId.length">
        <h4 class="viewed-products__title">История просмотров</h4>
        <div class="viewed-products__content" ref="contentContainer">
            <button
                    v-if="left < 0 && !isMobileScreen"
                    class="arrow left"
                    @click="moveToRight"
            >
                <fa class="icon" icon="arrow-left"></fa>
            </button>
            <button
                    v-if="rightSideDiff > 0 && !isMobileScreen"
                    class="arrow right"
                    @click="moveToLeft"
            >
                <fa class="icon" icon="arrow-right"></fa>
            </button>
            <div class="viewed-products__row" ref="viewedContainer" :style="translateX">
                <div
                        class="viewed-products__item"
                        :style="{width: itemWidth}"
                        v-for="sku in viewedSkus"
                        :key="sku.id"
                >
                    <div class="slider-product">
                        <div   class="slider-product__img">
                            <img

                                    v-lazyload
                                    :data-src="sku.image"
                                    :alt="sku.image">
                        </div>
                        <router-link
                                class="slider-product__name ui-link"
                                :to="{ name: 'description', params: { product_code: sku.code + '-' + sku.id }}"
                        >
                            <span>{{ sku.name }} {{ sku.volume }}</span>
                        </router-link>

                        <div class="slider-product__branch">
                            <div
                                    :class="{'pink': compared.includes( sku.id) }"
                                    class="slider-product__branch-item branch__item"
                                    @click="comparisonStore.toggleComparison(sku)"
                            >
                                Сравнить
                            </div>
                            <div class="slider-product__branch-item branch__item">
                                <router-link
                                        :to="{ name: 'reviews', params: { product_code: sku.code + '-' + sku.id }}"
                                >
                                    <preciseRatingView v-if="sku.rating " :rating="sku.rating "></preciseRatingView>
                                    <span v-if="sku.reviews_count">{{ sku.reviews_count }}</span>
                                </router-link>
                            </div>
                        </div>
                        <div class="slider-product__price">
                            <div class="slider-product__price-text">
                               от {{ sku.min_price }} руб
                            </div>

                            <router-link
                                    :to="{ name: 'description', params: { product_code: sku.code + '-' + sku.id}}"
                                    class="slider-product__price-icon"
                            >
                                <svg class="svg-inline--fa fa-chart-line fa-w-16 goto-price-charts__icon" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chart-line" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-v-3332190b="">
                                    <path class="" fill="currentColor" d="M496 384H64V80c0-8.84-7.16-16-16-16H16C7.16 64 0 71.16 0 80v336c0 17.67 14.33 32 32 32h464c8.84 0 16-7.16 16-16v-32c0-8.84-7.16-16-16-16zM464 96H345.94c-21.38 0-32.09 25.85-16.97 40.97l32.4 32.4L288 242.75l-73.37-73.37c-12.5-12.5-32.76-12.5-45.25 0l-68.69 68.69c-6.25 6.25-6.25 16.38 0 22.63l22.62 22.62c6.25 6.25 16.38 6.25 22.63 0L192 237.25l73.37 73.37c12.5 12.5 32.76 12.5 45.25 0l96-96 32.4 32.4c15.12 15.12 40.97 4.41 40.97-16.97V112c.01-8.84-7.15-16-15.99-16z"></path>
                                </svg>
                            </router-link>
                        </div>
                        <div class="slider-product__remove-product">
                            <tool-tip :position="'top'">
                                <template v-slot:content>
                                    Удалить из истории
                                </template>
                                <div  @click="viewedSkuStore.deleteFromViewedSkuIds(sku.id)">✕</div>
                            </tool-tip>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import toolTip from '../../components/tool-tip'
    import preciseRatingView from '../../components/preciseRatingView'
    import { storeToRefs } from "pinia";
    import {useViewedSkuStore} from "../../store/viewedSku";
    import {useComparisonStore} from "../../store/comparison";
    import {computed, onBeforeMount, onMounted, ref, watch} from "vue";

    let viewedContainer = ref(null);
    //let contentContainer = ref(null);
    let left = ref(0);


    const  viewedSkuStore = useViewedSkuStore();
    const  comparisonStore = useComparisonStore();
    const  { viewedSkuId, viewedSkus } = storeToRefs(viewedSkuStore);
    const  { compared } = storeToRefs(comparisonStore);


    //let contentWidth = computed(() => contentContainer.value.clientWidth);
    let containerWidth = computed(() => {
        if (viewedContainer.value) {
            return viewedContainer.value.clientWidth;
        }
        return 0;
    });
    const translateX = computed(() => `transform: translateX(${left.value}px)`);

    const rightSideDiff = computed(() => {
        const contentWidth = viewedSkus.value.length * (containerWidth.value / blockCount.value);
        return contentWidth + left.value - containerWidth.value;
    });

    const blockCount = computed(() => {
        const width = document.documentElement.clientWidth
        if (width < 900) {
            return 3;
        }

        return 4;
    });

    const isMobileScreen = computed(() => document.documentElement.clientWidth < 900);

    const itemWidth = computed(() => {
            if (!isMobileScreen.value) {
                return `calc(100% / ${blockCount.value}`;
            }
            return `250px`;
        }
    );

    const moveToLeft = () =>  {
        if (rightSideDiff.value > 0) {
            left.value =  left.value - containerWidth.value / blockCount.value;
        }
    };

    const moveToRight = () => {
        if (left.value < 0) {
            left.value =  left.value + containerWidth.value / blockCount.value;
        }
    }

    watch(viewedSkuId,() => viewedSkuStore.loadViewedSkus(), { deep: true});

    onBeforeMount(async () => {
        await viewedSkuStore.checkViewedSkuIds();
        await viewedSkuStore.loadViewedSkus();
    });

    // onMounted(async () => {
    //
    //
    // });
</script>

<style lang="scss" scoped>
    .viewed-products {
        &__title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 16px;
        }
        &__content {
            padding: 24px;
            position: relative;
            border-radius: 8px;
            box-shadow: 0 1px 2px 0 rgba(0,0,0,0.16);
            background-color: #fff;
            overflow-x: hidden;
            overflow-y: hidden;
            &:hover .arrow {
                opacity: 1;
                transform: translateX(0) translateY(- 50%);
            }
        }
        &__row {
            margin-right: -24px;
            display: flex;
            transition: transform .2s ease-out;
        }
        &__item {
            padding-right: 24px;
            flex-shrink:0;
            /*min-height: 364px;*/
        }


        .arrow {
            border:none;
            position: absolute;
            top: 50%;
            border-radius: 50%;
            box-shadow: 0 5px 20px 0 rgba(0,0,0,.2),inset 0 0 0 1px rgba(0,0,0,.1);
            opacity: 0;
            padding: 0;
            width: 48px;
            height: 48px;
            line-height: 40px;
            transition: all .12s ease-out;
            background-color: #fff;
            cursor: pointer;
            z-index:2;
            &:hover {
                background-color: #f5c423;
            }
        }
        .right {
            transform:translateX(10px) translateY(-50%);
            right: 20px;
        }
        .left {
            transform:translateX(-10px) translateY(-50%);
            left: 20px;
        }
    }

    .ui-link {
        text-decoration: none;
        transition-duration: .3s;
        transition-property: color;
    }

    .slider-product {
        display: grid;
        grid-gap: 12px;
        grid-template-columns: 1fr;
        grid-template-rows: 160px auto 32px 44px;
        height:100%;
        position: relative;
        width: 100%;
        &:hover .slider-product__price-icon {
            background-image: linear-gradient(180deg,#ffbc0b,#ff7400);
            border-color: #ffbc0b transparent #ff7400;
            box-shadow: inset 0 -2px 0 0 rgba(0,0,0,.2);
            color: #fff;
        }
        &__img {
            align-items: center;
            grid-row: 1;
            display: flex;
            justify-content: center;
            position: relative;
            text-align: center;
            width: 160px;
            height:160px;
            & img {
                object-fit: cover;
                max-width: 100%;
                max-height:100%;
            }
        }
        &__name {

            width: 100%;
            overflow: hidden;
            grid-column: 1;
            grid-row: 2;
            position: relative;
            color: #000;
            font-size: 16px;
            line-height: 22px;
            text-align: justify;
            & span {
                transition: max-height .3s;
            }
            &:after {
                background: linear-gradient(hsla(0,0%,100%,0),#fff);
                content: "";
                height: 30px;
                left: 0;
                position: absolute;
                top: 64px;
                width: 100%;
            }
        }
        &__branch {
            grid-row: 3;
            width: 100%;
            display: flex;
            align-self: end;
            justify-content: space-between;
            &-item {
                & a {
                    text-decoration: none;
                    color: #333;
                }
                &:not(:last-child) {
                    margin-right: 15px;
                }
            }
        }
        &__price {
            width: 100%;
            grid-row: 4;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            &-text {
                font-size: 18px;
                font-weight: 700;
                line-height: 20px;
            }
            &-icon {
                font-weight: 400 !important;
                height: 44px;
                line-height: 42px;
                border: 1px solid #d9d9d9;
                width: 44px;
                color: #8c8c8c;
                border-radius: 8px;
                cursor: pointer;
                display: flex;
                justify-content: center;
                align-items: center;
                font-size: 16px;
            }
        }
        &__remove-product {
            align-content: center;
            color: #d9d9d9;
            cursor: pointer;
            display: flex;
            font-size: 24px;
            height: 24px;
            justify-content: center;
            position: absolute;
            right: 0;
            top: 0;
            width: 24px;
        }
    }

    .branch__item {
        margin-top: 10px;
        padding: 6px 15px;
        border: 1px solid #eee;
        border-radius: 8px;
        font-size: 14px;
        line-height: 18px;
        cursor:pointer;
        &-selected {
            border: 1px solid #fc675c;
        }
        &:not(.branch__item-selected):hover {
            border: 1px solid #46bd87;
        }
    }
    @media (max-width: 900px) {
        .viewed-products {
            &__content {
                overflow-x:auto;
                overflow-y:hidden;
                &:hover .arrow {
                    opacity: 0;
                }
            }
            &__item {
                padding-right: 15px;
            }
        }
        .slider-product {
            &__name {
                font-size: 14px;
                line-height: 18px;
            }
        }
    }

</style>
