<template>
    <h2 class="title">{{ $route.meta.title }}</h2>

    <div class="comparison__category">
        <div
                class="comparison__categoryItem"
                :class="{'comparison__categoryItem-active': category.category_id === currentCategoryId }"
                v-for="category in compared"
                :key="category.category_id"
                @click="setCurrentCategory(category.category_id)"
        >
            {{ category.name }}
            <span>{{ category.ids.length }}</span>
        </div>
    </div>

    <div class="comparison" ref="comparisonContainer">
        <div class="comparison__layer" v-if="isLoadingComparedSkus">
            <loader class="loader"/>
        </div>
        <div class="comparison__wrapper" v-if="compared.length">

            <div class="comparison__header">
                <span>Показывать:</span>
                <span>Различающиеся характеристики</span>
                <span>Все характеристики</span>
                <span class="delete-list" @click="comparisonStore.removeIdsInCurrentCategory">
                     <fa class="icon" icon="trash"></fa>
                    Удалить список
                </span>
            </div>
            <div class="comparison__name">
                <button
                        v-if="!isMobileScreen && left < 0"
                        class="arrow left"
                        @click="moveToRight"
                >
                    <fa class="icon" icon="arrow-left"></fa>
                </button>
                <button
                        v-if="!isMobileScreen && rightSideDiff > 0"
                        class="arrow right"
                        @click="moveToLeft"
                >
                    <fa class="icon" icon="arrow-right"></fa>
                </button>

                <div id="content" class="comparison__row" :style="translateX"  :class="{'no-transition': currentShiftX !== 0}">
                    <div
                        @touchstart="isMobileScreen ? startMobile($event) : ''"
                        @touchmove="isMobileScreen ? moveMobile($event) : ''"
                        @touchend="isMobileScreen ? endMobile() : ''"
                        class="comparison__item"
                        v-for="sku in comparedSkus"
                        :key="sku.id"
                    >
                        <div class="comparison__item-layer">
                            <div
                                    class="comparison__item-add-to-favorite"
                                    @click="addToFav(sku.id)"
                            >
                                <fa class="icon" icon="star"></fa>
                            </div>
                            <div
                                    class="comparison__item-delete"
                                    @click="comparisonStore.removeFromComparedSku(sku)"
                            >
                                <fa class="icon" icon="trash"></fa>
                            </div>
                        </div>
                        <div class="comparison__img">
                            <img :src="sku.image" :alt="sku.image">
                        </div>
                        <router-link :to="{ name: 'description', params: { product_code: sku.code + '-' + sku.id }}">
                            {{ sku.name }}
                        </router-link>
                     </div>
                </div>
            </div>
            <div class="comparison__block">
                <div class="comparison__row" :style="translateX" :class="{'no-transition': currentShiftX !== 0}">
                    <div
                        @touchstart="isMobileScreen ? startMobile($event) : ''"
                        @touchmove="isMobileScreen ? moveMobile($event) : ''"
                        @touchend="isMobileScreen ? endMobile() : ''"
                        class="comparison__item"
                        v-for="sku in comparedSkus"
                        :key="'rating' + sku.id"
                    >
                        <span class="comparison__rating-count"  v-if="sku.reviews_count === 0">Нет отзывов</span>
                        <div class="comparison__item-rating" v-else>
                            <span class="comparison__rating">{{ sku.rating }}</span>
                            <span class="comparison__rating-count">{{ sku.reviews_count + ' ' + getReviewText(sku.reviews_count) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="comparison__block">
                <div class="comparison__row" :style="translateX" :class="{'no-transition': currentShiftX !== 0}">
                    <div
                        @touchstart="isMobileScreen ? startMobile($event) : ''"
                        @touchmove="isMobileScreen ? moveMobile($event) : ''"
                        @touchend="isMobileScreen ? endMobile() : ''"
                        class="comparison__item"
                        v-for="sku in comparedSkus"
                        :key="'price' + sku.id"
                    >
                        <div>
                            <strong>
                                <span v-if="sku.prices_count > 1">от</span>
                                {{ sku.min_price }} ₽
                            </strong>
                        </div>
                        <router-link :to="{ name: 'price', params: { product_code: sku.code + '-' + sku.id }}">
                            {{ sku.prices_count }} предложения
                        </router-link>
                    </div>
                </div>
            </div>
            <div class="comparison__block comparison__block-padding">
                <div class="comparison__property">Страна</div>
                <div class="comparison__row" :style="translateX" :class="{'no-transition': currentShiftX !== 0}">
                    <div
                        @touchstart="isMobileScreen ? startMobile($event) : ''"
                        @touchmove="isMobileScreen ? moveMobile($event) : ''"
                        @touchend="isMobileScreen ? endMobile() : ''"
                        class="comparison__item"
                        v-for="sku in comparedSkus"
                        :key="'country' + sku.id"
                    >
                        <span v-if="sku.country">{{ sku.country }}</span>
                        <span v-else class="no-data">Нет данных</span>
                    </div>
                </div>
            </div>
            <div class="comparison__block comparison__block-padding">
                <div class="comparison__property">Бренд</div>
                <div class="comparison__row" :style="translateX" :class="{'no-transition': currentShiftX !== 0}">
                    <div
                        @touchstart="isMobileScreen ? startMobile($event) : ''"
                        @touchmove="isMobileScreen ? moveMobile($event) : ''"
                        @touchend="isMobileScreen ? endMobile() : ''"
                        class="comparison__item"
                        v-for="sku in comparedSkus"
                        :key="'brand' + sku.id"
                    >
                        <span v-if="sku.brand">{{ sku.brand }}</span>
                        <span v-else class="no-data">Нет данных</span>
                    </div>
                </div>
            </div>
            <div class="comparison__block comparison__block-padding">
                <div class="comparison__property">Объем</div>
                <div class="comparison__row" :style="translateX" :class="{'no-transition': currentShiftX !== 0}">
                    <div
                        @touchstart="isMobileScreen ? startMobile($event) : ''"
                        @touchmove="isMobileScreen ? moveMobile($event) : ''"
                        @touchend="isMobileScreen ? endMobile() : ''"
                        class="comparison__item"
                        v-for="sku in comparedSkus"
                        :key="'volume' + sku.id"
                    >
                        {{ sku.volume }}
                    </div>
                </div>
            </div>
            <div class="comparison__block comparison__block-padding">
                <div class="comparison__property">Активные ингредиенты</div>
                <div class="comparison__row" :style="translateX" :class="{'no-transition': currentShiftX !== 0}">
                    <div
                        @touchstart="isMobileScreen ? startMobile($event) : ''"
                        @touchmove="isMobileScreen ? moveMobile($event) : ''"
                        @touchend="isMobileScreen ? endMobile : ''"
                        class="comparison__item"
                        v-for="sku in comparedSkus"
                        :key="'ingredient' + sku.id"
                    >
                        {{ sku.ingredients.join(', ') }}
                    </div>
                </div>
            </div>

        </div>

        <div  class="comparison__wrapper comparison__wrapper-center" v-else>
            0
            <img src="/storage/icons/libra.svg" class="comparison__icon" alt="Список пуст">
            <h2 class="comparison__title">Список сравниваемых товаров пока пуст</h2>
            <p class="comparison__text">Вы можете начать свой выбор с нашего каталога товаров или воспользоваться поиском, если ищете что-то конкретное.</p>
            <btn>
                <router-link class="choice" :to="{ name: 'main' }" >Выбрать товары</router-link>
            </btn>
        </div>
    </div>
    <viewed-products></viewed-products>
</template>

<script setup>
    import btn from '../../components/btn'
    import viewedProducts from '../../components/viewed-products'
    import loader from "../../components/loader";
    import { storeToRefs } from "pinia";
    import { ref, computed, onMounted, watch } from 'vue';
    import {useComparisonStore} from "../../store/comparison";
    import {useAuthStore} from "../../store/auth";
    import {useFavoritesStore} from "../../store/favorites";

    let comparisonContainer = ref(null);
    let left = ref(0);
    let shiftX = ref(0);
    let currentShiftX = ref(0);
    let containerWidth = 0;
    const itemWidth = 200;

    const comparisonStore = useComparisonStore();
    const authStore = useAuthStore();
    const favoritesStore = useFavoritesStore();
    const { compared, comparedSkus, isLoadingComparedSkus, currentCategoryId } = storeToRefs(comparisonStore);
    const { isAuth } = storeToRefs(authStore);
    const { favorites } = storeToRefs(favoritesStore);


    const isMobileScreen = computed(() => document.documentElement.clientWidth < 800);
    const translateX = computed(() => `transform: translateX(${left.value + currentShiftX.value}px)`);
    const rightSideDiff = computed(() => {
        const contentWidth = comparedSkus.value.length * itemWidth;
        return contentWidth + left.value - containerWidth;
    });

    const setCurrentCategory = id => {
        comparisonStore.setCurrentCategoryId(id);
        left.value = 0;
    };

    const addToFav = id => {
        if (!isAuth.value) {
            authStore.setIsShowAuthModal(true);
        } else {
            if (!favorites.value.includes(id)) {
                favoritesStore.addToFavorites(id);
            } else {
                favoritesStore.removeFromFavorites(id);
            }
        }
    };

    const getReviewText = count => {
        switch (count) {
            case 1:
                return 'отзыв';
            case 2:
            case 3:
            case 4:
                return 'отзыва';
            default:
                return 'отзывов';
        }
    };
    const moveToLeft = () => {
        if (rightSideDiff.value > 0) {
           left.value =  left.value - itemWidth;
        }
    };
    const moveToRight = () => {
        if (left.value < 0) {
            left.value =  left.value + itemWidth;
        }
    };

    const startMobile = (event) => shiftX.value = event.changedTouches[0].pageX;

    const moveMobile = (event)  => currentShiftX.value = event.changedTouches[0].pageX - shiftX.value;

    const endMobile = () => {
        const diff = itemWidth * Math.round(currentShiftX.value / itemWidth);
        let leftLocal =  left.value + diff;

        if (leftLocal > 0) {
            leftLocal = 0;
        }
        if (leftLocal < -((comparedSkus.value.length - 2) * itemWidth)) {
            leftLocal = -((comparedSkus.value.length - 2) * itemWidth);
        }
        left.value = leftLocal;
        currentShiftX.value = 0;
    }


    watch(currentCategoryId,() => comparisonStore.loadComparedSkus());

    onMounted(() => {
        comparisonStore.loadComparedSkus();

        if (comparisonContainer) {
            containerWidth = comparisonContainer.value.clientWidth;
        }
    });
</script>

<style lang="scss" scoped>
    .comparison {
        position: relative;
        margin: 20px 0;
        display: flex;
        width: 100%;
        flex-flow: column nowrap;
        align-items: center;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 6px 40px 0 rgba(56,69,94,.07);
        padding: 34px 0;
        &__header {
            padding: 0 20px;
            & span {
                margin-right: 20px;
            }
        }
        &__category {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            border: 1px solid rgba(0,0,0,.2);
            border-radius: 4px;
        }
        &__categoryItem {
            white-space: nowrap;
            cursor: pointer;
            display: inline-block;
            line-height: 36px;
            height: 36px;
            padding: 0 16px;
            &:not(:last-child) {
                border-right: 1px solid rgba(0,0,0,.2);
            }
            &-active {
                background: #ffeb99;
            }
        }
        &__layer {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: hsla(0,0%,100%,.61);
            display: flex;
            justify-content: center;
            z-index:3;
        }
        &__wrapper {
            width: 100%;
            display: flex;
            justify-content: flex-start;
            flex-flow: column nowrap;
            overflow: hidden;
            &-center {
                align-items: center;
            }
        }
        &__rating {
            position: relative;
            display: inline-block;
            padding: 1px 3px 0 6px;
            margin-right: 6px;
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
        &__block {
            padding: 20px;
            position: relative;
            width: 100%;
            border-top: 1px solid #e8e8e8;
            transition: background .15s ease-out;
            &-padding {
                padding: 42px 20px 20px;
            }
            &:hover {
                background: #f4f4f4;
            }
        }
        &__row {
            width: 100%;
            display: flex;
            transition: transform .2s ease-out;
        }
        &__name {
            padding: 20px;
            position: relative;
            width: 100%;
        }
        &__property {
            position: absolute;
            top: 17px;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: grey;
            font-size: 11px;
        }

        &__img {
            margin-bottom: 6px;
            width: 80px;
            height: 80px;
            &  img {
                max-width: 100%;
                max-height: 100%;
                display: flex;
                justify-content: center;
                align-items: center;
            }
        }

        &__volume {

        }
        &__item {
            flex-shrink: 0;
            position: relative;
            padding-right: 30px;
            width: 200px;
            &:hover .comparison__item-layer {
                opacity: 1;
                visibility: visible;
            }
            &-layer {
                position: absolute;
                opacity: 0;
                visibility: hidden;
                top: 0;
                right: 13px;
                width: 30px;
                transition: all .15s ease-out;
            }
            &-add-to-favorite,
            &-delete {
                width: 26px;
                height: 26px;
                cursor:pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #898f97;
            }
        }
        &__icon {
            display: block;
            width: 100px;
            height: 100px;
            object-fit: contain;
            flex-shrink: 0;
            margin-bottom: 30px;
        }
        &__title {
            text-align: center;
            font-size: 26px;
            font-weight: 700;
            color: #374957;
            margin-bottom: 16px;
        }
        &__text {
            font-size: 15px;
            text-align: center;
            width: 100%;
            max-width: 550px;
            margin-bottom: 30px;
        }
    }
    a {
        text-decoration: none;
    }
    .choice {
       color: #fff;
    }
    .no-data {
        color: #aaa;
    }
    .arrow {
        border:none;
        position: absolute;
        top: 35px;
        border-radius: 50%;
        padding: 0;
        width: 40px;
        height: 40px;
        line-height: 40px;
        transition: all .12s ease-out;
        background-color: #fc0;
        cursor: pointer;
        z-index:2;
        &:hover {
            background-color: #f5c423;
        }
    }
    .right {
        right: 20px;
    }
    .left {
        left: 20px;
    }
    .delete-list {
        cursor:pointer;
        &:hover{
            color: red;
        }
    }
    .loader {
        margin-top: 100px;
        width: 100px;
        height: 100px;
    }
    .no-transition {
        transition: none;
    }
</style>