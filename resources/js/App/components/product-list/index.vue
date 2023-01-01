<template>
    <div class="product-list" id="productList">
        <div
                v-if="isLoadingProductsWithPagination && (!isEmptyFilterOptions ||sortOption !== 'popular')"
                class="loader-wrapper"
        >
            <loader class="loader"/>
        </div>


        <top-block/>



        <div
                v-if="isLoadingProductsWithPagination && !isLoadMore"
                class="product"
                :class="{'view-full': viewMode === 'full', 'view-compact': viewMode === 'compact'}"
        >
            <div
                    class="product__item skeleton-loader"
                    v-for="el in [...Array(12).keys()]"
                    :key="el"
            >
            </div>
        </div>


        <div
                v-else
                class="product"
                :class="{'view-full': viewMode === 'full', 'view-compact': viewMode === 'compact'}"
        >
            <div
                    class="product__item"
                    v-for="(product, index) in productsWithPagination"
                    :key="index"
            >

                <router-link
                        class="product__image"
                        :to="{ name: 'description', params: { product_code: product.code + '-' + product.currentSku.id }}"
                >
                    <image-slider
                            class="product__slider"
                            :photos="product.currentSku.images"
                            :alt="product.name"
                    />
                </router-link>


                <div class="product__name">
                    <router-link
                            :to="{ name: 'description', params: { product_code: product.code + '-' + product.currentSku.id }}"
                    >
                        {{ product.brand }}  {{ product.name }}
                    </router-link>
                </div>

                <div class="product__volumes">
                    <div
                            class="product__volumes-item"
                            :class="{'product__volumes-selected': sku.id === product.currentSku.id }"
                            v-for="sku in product.skus"
                            :key="sku.id"
                    >
                        <span>
                            {{ sku.volume }}
                        </span>
                    </div>
                </div>

                <div class="product__branch">
                    <router-link
                            class="product__branch-item branch__item"
                            :to="{ name: 'reviews', params: { product_code: product.code + '-' + product.currentSku.id }}"
                    >
                        <preciseRatingView v-if="product.currentSku.rating"
                                           :rating="product.currentSku.rating"></preciseRatingView>
                        <span v-if="product.currentSku.reviews_count">{{ product.currentSku.reviews_count }}</span>
                    </router-link>

                    <router-link
                            class="product__branch-item branch__item"
                            :to="{ name: 'questions', params: { product_code: product.code + '-' + product.currentSku.id }}"
                    >
                        <svg class="branch__icon" viewBox="0 0 24 24">
                            <path d="M18 7.998H6v2h12v-2zM17.998 11.998H6.002v2h11.996v-2z"></path>
                            <path d="M3.404 19.997h5.599l2.996 3.997L15 19.997h5.601a1.4 1.4 0 0 0 1.4-1.4l-.002-14.199a1.4 1.4 0 0 0-1.4-1.4H3.402a1.4 1.4 0 0 0-1.4 1.4l.002 14.199a1.4 1.4 0 0 0 1.4 1.4zM20 4.999L20 17.997h-6l-.6.8L12 20.66l-1.397-1.864-.6-.8H4.004L4.002 5h15.997z"></path>
                        </svg>
                    </router-link>
                </div>

                <div class="product__buy">
                    <div class="product__price-title">
                        <a v-if="product.currentSku.prices.length"  href="#" class="product__price-range">
                            <span v-if="product.currentSku.prices.length > 1">
                                от
                                <span class="product__price-digit">{{ product.currentSku.minPrice }} </span>
                                до
                            </span>
                            <span>
                                <span class="product__price-digit">{{ product.currentSku.maxPrice }}</span>
                                 р.
                            </span>


                            <img
                                    v-lazyload
                                    v-if="product.currentSku.prices.length === 1"
                                    :data-src="product.currentSku.prices[0].image"
                                    :alt="product.currentSku.prices[0].name">

                        </a>
                        <div v-else>Ожидается в продаже</div>


                    </div>
                    <div class="product__prices">
                        <div
                                class="product__prices-item"
                                v-for="(price, index) in product.currentSku.prices"
                                :key="index"
                        >
                            <a :href="'/to/' + price.code">
                                <span class="product__prices-link">{{ price.name }}</span>
                                <span class="price__arrow" v-if="product.currentSku.prices.length > 1">→</span>
                            </a>

                            <a v-if="product.currentSku.prices.length > 1" :href="'/to/' + price.code">{{ price.price }} р.</a>
                            <a v-else :href="'/to/' + price.code" class="product__price-btn">Купить</a>

                        </div>
                    </div>
                    <router-link
                            v-if="product.currentSku.prices.length > 1"
                            class="product__price-btn"
                            :to="{ name: 'price', params: { product_code: product.code + '-' + product.currentSku.id }}"
                    >
                        <span>Сравнить цены</span>
                        <sub>{{ product.currentSku.prices.length }}</sub>
                        →
                    </router-link>
                </div>
                <div class="product__layer">
                    <div
                            class="icon"
                            :class="{'icon__pink': favorites.includes( product.currentSku.id) }"
                            @click="addToFav(product.currentSku.id)"
                    >
                       <svg  v-if="!favorites.includes( product.currentSku.id)"
                            viewBox="0 0 24 24">
                                <path d="M12 4.367C10.675 3.28 9.245 2.72 7.73 2.72A5.73 5.73 0 0 0 2 8.45c0 4.329 3.197 8.531 9.448 12.664l.552.365.551-.365C18.803 16.981 22 12.778 22 8.45a5.73 5.73 0 0 0-5.73-5.73c-1.515 0-2.945.56-4.27 1.648zM4 8.449a3.73 3.73 0 0 1 3.73-3.73c1.223 0 2.402.551 3.566 1.705l.704.698.704-.698c1.164-1.154 2.343-1.704 3.567-1.704A3.73 3.73 0 0 1 20 8.45c0 3.397-2.623 6.956-8 10.626-5.377-3.67-8-7.23-8-10.627z"></path>
                       </svg>
                        <fa  v-else icon="heart"></fa>
                    </div>
                    <div
                            class="icon"
                            :class="{'icon__pink': allComparedSkuIds.includes( product.currentSku.id) }"
                            @click="getComparison(product)"
                    >
                        <svg  height="24" width="24" viewBox="0 0 24 24">
                            <path d="M3 7V4h2v3l3 .001v2H5V12H3V9H0V7h3zm5 6.004v-2h13v2H8zM10 7v2h11V7H10zM3 17.002v-2h18v2H3zM3 19v2h18v-2H3z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <button
                id="loadMore"
                v-if="lastPage > 1 && currentPage !== lastPage"
                class="more"
                @click="loadMore"
        >
            <span v-if="!isLoadMore">Показать еще</span>
            <span v-else>
                <loader/>
            </span>
        </button>

        <pagination
                v-if="productsWithPagination.length && lastPage > 1"
                v-model:currentPage="currentPageLocal"
                :lastPage="lastPage"
        />

    </div>
</template>

<script setup>
    import pagination from '../pagination'
    import loader from '../loader'
    import preciseRatingView from '../preciseRatingView'
    import topBlock from './top-block'
    import imageSlider from '../image-slider'
    import {defineProps, defineEmits, ref, reactive, computed, onMounted, watch} from "vue";
    import {storeToRefs} from "pinia";
    import {useProductStore} from "../../store/product";
    import {useAuthStore} from "../../store/auth";
    import {useFavoritesStore} from "../../store/favorites";
    import {useComparisonStore} from "../../store/comparison";
    import {useRoute, useRouter} from "vue-router";

    const productStore = useProductStore();
    const authStore = useAuthStore();
    const favoritesStore = useFavoritesStore();
    const comparisonStore = useComparisonStore();
    const { isLoadingProductsWithPagination, productsWithPagination, currentCategory,
        lastPage, currentPage, filterOptions, sortOption, viewMode, isLoadMore, isEmptyFilterOptions } = storeToRefs(productStore);
    const { isAuth } = storeToRefs(authStore);
    const { favorites } = storeToRefs(favoritesStore);
    const { allComparedSkuIds } = storeToRefs(comparisonStore);


    const route = useRoute();
    const router = useRouter();
    const currentPageLocal = computed({
        get() {
            return currentPage.value;
        },
        set(value) {
            setPageQuery(value);
        }
    });


    const calculateProductCode = product =>  product.code + '-' + product.currentSku.id;

    const setPageQuery = value => {
        const query = {...route.query}

        if (value > 1) {
            query.page = value
        } else if (value === 1) {
            delete query.page
        }
        router.push({query})
    };

    const loadMore = async () => {
        productStore.setIsLoadMore(true);
        const query = {...route.query};
        query.page = currentPage.value + 1;
        await router.push({query});
    };

    const getComparison = sku => {
        comparisonStore.toggleComparison({
            id: sku.currentSku.id,
            name: sku.name,
            image: sku.currentSku.images[0],
            category_id: sku.category_id,
            category: sku.category,
            volume: sku.skus.find(el => el.id === sku.currentSku.id).volume
        })
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
</script>

<style lang="scss" scoped>
    @keyframes load {
        from {
            left: -150px;
        }
        to {
            left: 100%;
        }
    }

    .more {
        margin-top: 20px;
        margin-bottom: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.16);
        background-color: #fff;
        border: 0;
        height: 48px;
        color:#8c8c8c;
        font-size: 16px;
        font-weight: bold;
        text-align: center;
        width: 100%;
        & svg {
            width: 40px;
            height: 40px;
        }
    }
    a {
        text-decoration: none;
    }
    .product-list {
        position: relative;
    }

    .loader {
        margin-top: 100px;
        width: 200px;
        height: 200px;

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



    .skeleton-loader {
        position:relative;
        overflow: hidden;
        &:before {
            content: '';
            display: block;
            position: absolute;
            left: -150px;
            top: 0;
            height: 100%;
            width: 150px;
            background: linear-gradient(to right, transparent 0%, #E8E8E8 50%, transparent 100%);
            animation: load 1s cubic-bezier(0.4, 0.0, 0.2, 1);
        }
    }

    .product {
        display: grid;
        &__item {
            background-color: #fff;
            position: relative;
            border-radius: 8px;
            box-shadow: 0 1px 2px 0 rgba(0,0,0,0.16);
            width: 100%;
        }


        &.view-full {
            display: grid;
            grid-gap: 10px;
            grid-template-columns: 1fr;

            & .skeleton-loader {
                height:210px;
            }

            & .product__item {
                display:grid;
                padding:15px;
                grid-gap: 10px;
                grid-column-gap: 20px;
                grid-template-columns: auto minmax(0, 1fr) 240px;
                grid-template-rows: 1fr auto auto;
                /*grid-template-areas:*/
                /*        'image name buy'*/
                /*        'image volumes buy'*/
                /*        'image branch buy'*/
                /*;*/
                /*grid-template-columns: 1fr 3fr 1fr;*/

                & .product__image {
                    grid-column: 1;
                    grid-row: 1/4;
                    max-width:180px;
                    height: 180px;
                    width: 180px;
                }

                & .product__name {
                    font-size:18px;
                    grid-column: 2;
                    grid-row: 1;
                }
                & .product__volumes {
                    grid-column: 2;
                    grid-row: 2;
                }
                & .product__branch {
                    grid-column: 2;
                    grid-row: 3;
                }
                & .product__branch-item {
                    margin-right: 10px;
                }
                & .product__buy {
                    grid-column: 3;
                    grid-row: 1/4;
                    margin-right: 40px;
                }
            }
        }

        &.view-compact {
            display:grid;
            grid-gap: 10px;
            grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));

            & .skeleton-loader {
                height: 556px;
            }

            & .product__item {
                padding: 12px;
                display:grid;
                grid-gap: 10px;
                grid-template-columns: 1fr;
                grid-template-rows: 200px 1fr auto auto auto;

                & .product__image {
                    grid-row: 1;
                    align-items: center;
                    align-content: center;

                }

                & .product__name {
                    grid-row: 2;
                    grid-column: 1;
                    max-height: 110px;
                }
                & .product__volumes {
                    grid-row: 3;
                }
                & .product__branch {
                    grid-row: 4;
                    flex-wrap: wrap;
                    justify-content: space-between;
                }

                & .product__buy {
                    grid-row: 5;
                }
            }
        }

        & .product__image {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;

            & img {
                max-width:100%;
                max-height:100%;
            }
        }

        &__name {

        }
        &__volumes {
            align-self: start;
            display: flex;
            &-item {
                padding: 3px 7px;
                color: #222;
                border-radius: 6px;
                font-size: 13px;
                border: 1px solid #eee;
                &:not(:last-child) {
                    margin-right: 15px;
                }
            }
            &-selected {
                border: 1px solid  #b5c1dc;
            }
        }
        &__branch {
            align-self: end;
            display: flex;
            &-item {
                display: flex;
                align-items:center;
                color: #333;
                & span {
                    color:#000;
                    margin-left:5px;
                }
            }
        }
        &__buy {
            display:flex;
            flex-direction:column;
            align-items: flex-start;
        }

        &__price {
            &-title {
                display: flex;
                width: 100%;
                align-items: flex-start;
                justify-content: space-between;
                margin-bottom: 10px;
                & img {
                    max-height: 30px;
                    max-width: 50%;
                }
            }

            &-range {
                width: 100%;
                display: flex;
                justify-content: space-between;
                align-self: flex-end;
                color: #ff8d02;
                font-size: 13px;

            }
            &-digit {
                font-size: 21px;
            }
            &-btn {
                margin-top: auto;
                padding: 5px 12px;
                border-radius: 8px;
                background-image: linear-gradient(to bottom, #ffd6e7, #f88cb6);
            }
        }

        &__prices {
            width: 100%;
            margin-bottom: 10px;
            margin-top:auto;
            &-item {
                display: flex;
                align-items: center;
                justify-content: space-between;
                & a:not(:last-child){
                    margin-right:10px;
                }
            }
            &-link {
                border-bottom: 1px solid #b5c1dc;
            }
        }
        &__layer {
            position: absolute;
            top: 12px;
            right: 12px;
            background-color: transparent;
        }
    }


    .branch {
        &__item {
            padding: 6px 8px;
            border: 1px solid #eee;
            border-radius: 8px;
            font-size: 14px;
            line-height: 18px;
            cursor:pointer;
            &:hover {
                border: 1px solid  #b5c1dc;
            }
        }

        &__icon {
            width: 18px;
            height: 18px;
        }
    }

    .pink {
        background-image: linear-gradient(to bottom, #ffd6e7, rgb(248, 140, 182));
    }
    .icon {
        width: 24px;
        height: 24px;
        cursor: pointer;
        justify-content: center;
        align-items: center;
        margin-left: auto;
        margin-bottom:3px;
        display: flex;


        &__pink svg {
            fill:red;
            color: red;
            width: 24px;
            height: 24px;
        }
    }



    @media (max-width: 768px) {
        .product {
            &.view-full {
                & .product__item {
                    padding: 10px;
                    grid-template-columns: auto auto;
                    grid-template-rows: auto auto auto auto;
                    & .product__image {
                        grid-row: 1/4;
                    }

                    & .product__name {
                        font-size: 14px;
                        margin-right:40px;
                    }

                    & .product__price-title img{
                        max-width: 30%;
                    }

                    & .product__volumes {
                        grid-column: 2;
                        grid-row: 2;
                    }
                    & .product__branch {
                        flex-wrap: wrap;
                        grid-column: 2;
                        grid-row: 3;
                    }
                    & .product__buy {
                        grid-column: 1/3;
                        grid-row: 4;
                        margin-right:0;
                    }
                }

            }
            &.view-compact {
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
                & .product__name {
                    font-size: 14px;
                }
            }
        }
        .branch {
            &__item {
                padding: 4px 10px;
            }
            &__icon {
                height: 16px;
                width: 16px;
            }
        }
    }
    @media (max-width: 540px) {
        .product {
            &.view-compact {
                grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
                & .product__name,
                & .product__buy {
                    font-size: 14px;
                }
                & .product__prices-item {
                    flex-wrap: wrap;
                }
            }
        }
    }

    @media (max-width: 420px) {
        .product {
            &.view-full {
                & .product__item {
                    grid-template-columns:minmax(120px, 1fr) auto;
                }
            }
            &.view-compact {
                grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
                & .product__name,
                & .product__buy {
                    font-size: 12px;
                }
                & .product__prices-item > a {
                    margin-bottom: 5px;
                }
            }
        }
    }
</style>