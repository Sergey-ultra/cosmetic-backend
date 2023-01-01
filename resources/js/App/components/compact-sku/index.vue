<template>
    <div class="compact">
        <div class="back">
            <router-link
                    class="back__link"
                    :to="{ name: 'description', params: { product_code: currentSkuProductCode }}"
            >
                <div class="back__icon">
                    <svg><use xlink:href="#icons_arrow-left">
                        <symbol viewBox="0 0 24 24" id="icons_arrow-left"><path fill-rule="evenodd" d="M20 11H7.824l5.583-5.583-1.414-1.414L3.996 12l7.997 7.997 1.414-1.414L7.824 13H20z"></path></symbol>
                    </use></svg>
                </div>
                <span>К описанию товара</span>
            </router-link>
        </div>
        <div class="compact__main">
            <section class="sku">
                <div
                        v-if="isLoadingCompactCurrentSku"
                        class="loader-wrapper"
                >
                    <loader class="loader"/>
                </div>
                <div class="sku__inner" v-else>
                    <div class="sku__image"  v-if="currentSkuLocal.images && currentSkuLocal.images.length">
                        <router-link :to="{name: 'description', params: { product_code: currentSkuProductCode }}">
                            <img :src="currentSkuLocal.images[0]" >
                        </router-link>
                    </div>
                    <div class="sku__text">
                        <router-link :to="{ name: 'description'}">
                            <h1 class="sku__title">
                                {{ currentSkuLocal.name }}, {{ currentSkuLocal.volume }}
                            </h1>
                        </router-link>
                        <div class="sku__row">
                            <div v-if="currentSkuLocal.reviews_count === 0" class="sku__url">
                                <router-link
                                    v-if="'reviews' !==  $route.name"
                                    :to="{ name: 'reviews'}"
                                    class="sku__link sku__link-active"
                                >
                                    Отзывы
                                </router-link>
                                <span v-else  class="sku__link">Нет отзывов</span>

                            </div>
                            <div v-else class="sku__url" >
                                <span class="sku__rating">{{ currentSkuLocal.rating }}</span>
                                <router-link
                                    v-if="'reviews' !==  $route.name"
                                    :to="{ name: 'reviews'}"
                                    class="sku__link sku__link-active"
                                >
                                    {{ reviewText }}
                                </router-link>
                                <span v-else  class="sku__link">{{ reviewText }}</span>
                            </div>
                            <router-link
                                v-if="'questions' !==  $route.name"
                                :to="{ name: 'questions' }"
                                class="sku__link sku__link-active"
                            >
                                Задать вопрос
                            </router-link>
                            <span v-else class="sku__link">Задать вопрос</span>
                        </div>
                    </div>
                </div>
            </section>
            <div class="compact__inner">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script setup>
    import loader from "../loader";
    import {useCurrentSkuStore} from "../../store/currentSku";
    import {computed, onMounted, watch} from "vue";
    import {storeToRefs} from "pinia";
    import {useRoute} from "vue-router";

    const currentSkuStore = useCurrentSkuStore();
    const { currentSku, compactCurrentSku, isLoadingCompactCurrentSku } = storeToRefs(currentSkuStore);

    const route = useRoute();

    const currentSkuLocal = computed(() => {
        if (!Object.keys(currentSku.value).length) {
            return compactCurrentSku.value;
        }
        return  currentSku.value;
    });

    const reviewText = computed(() => {
        let text = '';
        switch (currentSkuLocal.value.reviews_count) {
            case 1:
                text = 'отзыв';
                break;
            case 2:
            case 3:
            case 4:
                text = 'отзыва';
                break;
            default:
                text = 'отзывов';
        }
        return currentSkuLocal.value.reviews_count + ' ' + text;
    });

    const currentSkuProductCode = computed(() => currentSkuLocal.value.code + '-' + currentSkuLocal.value.id);

    const currentSkuId = computed(() => {
        if (route.params.product_code && route.params.product_code.includes('-')) {
            const urlParts = route.params.product_code.split('-');
            return Number(urlParts[urlParts.length - 1])
        }
        return null;
    });


    watch(currentSkuId, value => {
        if (value) {
            currentSkuStore.setCurrentSkuId(value);
            currentSkuStore.loadCompactCurrentSku();
        }
    });

    onMounted(() => {
        if (currentSkuId.value) {
            currentSkuStore.setCurrentSkuId(currentSkuId.value);
            currentSkuStore.loadCompactCurrentSku();
        }
    });
</script>

<style lang="scss" scoped>
    .compact {
        &__main {
            margin-top: 20px;
            border-radius: 11px;
            background-color: #fff;
            padding-bottom: 20px;
            overflow: hidden;
        }
        &__inner {
            padding: 0 20px;
        }
    }
    .loader {
        width: 70px;
        height: 70px;

        &-wrapper {
            display: flex;
            justify-content: center;
            position: absolute;
            z-index: 3;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.82);
        }
    }
    .back {
        margin-top: 30px;
        color: #04b;
        &__link {
            text-decoration: none;
            display: flex;
            align-items: center;
            color: #04b;
        }
        &__icon {
            display: inline-block;
            position: relative;
            width: 32px;
            height: 32px;
            & svg {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%,-50%);
                width: 20px;
                height: 20px;
                fill: #04b;
            }
        }
    }


    .sku {
        position: relative;
        min-height: 50px;
        &__inner {
            justify-content: space-between;
            display: flex;
            box-shadow: 0 2px 3px 0 rgba(0,0,0,.06);
            padding: 20px 20px 0;
        }
        &__image a {
            display: block;
            position: relative;
            width: 70px;
            height: 70px;
            margin: 0 16px 16px 0;
            border-right: none;
            & img {
                position: absolute;
                top: 50%;
                right: 0;
                bottom: 0;
                left: 50%;
                max-width: 100%;
                max-height: 100%;
                transform: translate(-50%,-50%);
            }
        }
        &__text {
            margin-top: 8px;
            margin-bottom: 16px;
            flex: 1;
        }
        &__title {
            margin: 0;
            padding: 0;
            color: #2b2b2b;
            display: inline-block;
            vertical-align: middle;
            font-size: 18px;
            line-height: 24px;
            font-weight: 700;
            &:hover {
                color: red;
            }
        }
        &__row {
            display: flex;
            margin-top: 8px;
            align-items: center;
            flex-wrap: wrap;
            width: 100%;
            transition: transform .2s ease-out;
        }
        &__url {
            display: inline;
        }
        &__rating {
            position: relative;
            display: inline-block;
            padding: 1px 3px 0 6px;
            margin-right: 12px;
            font-size: 13px;
            line-height: 21px;
            color: #fff;
            text-align: center;
            background-color: #359e00;

            &-count {
                margin-left: 8px;
                font-size: 13px;
                color: grey;
                line-height: 22px;
            }

            &:after {
                position: absolute;
                top: 0;
                left: 100%;
                display: block;
                content: "";
                border: solid;
                border-width: 11px 0 11px 6px;
                border-color: transparent;
                border-left-color: #359e00;
            }
        }
        &__link {
            text-decoration: none;
            margin-left: 0 !important;
            margin-right: 24px !important;
            white-space: nowrap;
            height: 24px;
            line-height: 24px;
            color: #000;
            &-active {
                color: #04b;
                font-weight: 400;
                font-style: normal;
                font-stretch: normal;
            }
        }
    }
    @media (max-width: 500px) {
        .compact {
            &__main {
                border-radius: 0;
                margin: 20px -8px 0;
            }
        }
    }

</style>
