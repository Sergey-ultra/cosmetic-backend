<template>
    <div class="product-page__top">
        <div class="product-page__title">
            <h1 class="title">{{ currentCategory ? currentCategory.name : '' }}</h1>
            <span class="product-page__count">{{  totalCount }} товаров</span>
        </div>
    </div>


    <div class="product-page__wrapper">
        <filter-block class="product-page__filter"/>
        <product-list class="product-page__items"/>
    </div>

    <viewed-products/>
    <popular-brands></popular-brands>
</template>

<script setup>
    import filterBlock from "../../components/filterBlock"
    import viewedProducts from '../../components/viewed-products'
    import productList from '../../components/product-list'
    import popularBrands from '../../components/popular-brands'
    import { storeToRefs } from "pinia";
    import {useProductStore} from "../../store/product";
    import {useFilterStore} from "../../store/filter";
    import {useRoute} from "vue-router";
    import {onMounted, watch} from "vue";

    const productStore = useProductStore();
    const filterStore = useFilterStore();
    const { currentCategory, totalCount,  filterOptions, productLoadingMode } = storeToRefs(productStore);
    const route =  useRoute();


    const loadProductsByCategoryCode = () => {
        productStore.loadProductsWithPagination();
        filterStore.loadFilters();
    };
    const setTitle = name => {
        if (name) {
            document.title = route.meta.title + ' ' + name +
                ' Smart-Beautiful - агрегатор цен косметических товаров';
        }
    };


    watch(currentCategory, (value) => {
            if (value) {
                filterStore.loadReceipts()
                setTitle(value.name)
            }
        }
    );

    watch(() => ({params: route.params, query: route.query}), (value, oldValue) => {
            if (value.params.category_code) {
                if (oldValue && oldValue.params.category_code !== value.params.category_code) {
                    productStore.setProductLoadingMode({category_code: value.params.category_code})
                }
                productStore.setProductParamsByProductQuery(value.query)
                loadProductsByCategoryCode()
            }
        },
        { deep: true }
    );

    onMounted(() => {
        if (route.params.category_code) {
            if (currentCategory) {
                setTitle(currentCategory.name)
            }
            productStore.setProductLoadingMode({ category_code: route.params.category_code })
            productStore.setProductParamsByProductQuery(route.query)
            loadProductsByCategoryCode()
        }
    });
</script>

<style lang="scss" scoped>
    a {
        text-decoration: none;
        color:#3757c5;
    }
    .breadcrumb {

        display: flex;
        padding-left: 0;
        padding-right: 0;
        border-radius: 0;
        font-size: 13px;
        /*background-color: transparent;*/
        margin-block-start: 0 !important;
        overflow: hidden;
        position: relative;
        max-height: 23px;
        margin-bottom: 8px;
        & a {
            color: #333;
        }

        &__item {
            list-style-type: none;
            white-space: nowrap;
        }

        & .breadcrumb__item + .breadcrumb__item::before {
            color: gray;
            content: "> ";
            padding: 0 5px;
        }
    }

    .title {
        color: #2c509a;
        display: inline-block;
        font-size: 32px;
        line-height: 32px;
        font-weight: bold;
        margin-top: 0;
        margin-bottom: 0;
        vertical-align: middle;
        margin-right: 12px;
    }

    .product-page {
        &__top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin: 50px 0 10px;
        }
        &__title {
            display: flex;
            align-items: center;
            margin-bottom:10px;
        }
        &__count {
            font-weight: bold;
            letter-spacing: .2px;
            font-size: 16px;
            display: inline-block;
            vertical-align: middle;
        }
        &__question {
            margin-bottom: 10px;
            border: 0;
            border-radius: 4px;
            position: relative;
            line-height: 36px;
            height: 36px;
            padding: 0 16px;
        }
        &__wrapper {
            display:flex;
            justify-content: space-between;
            align-items: flex-start;
            position: relative;
        }

        &__filter {
            z-index:5;
            position: relative;
            width: 272px;
            display: block;
            min-height:700px;
        }
        &__items {
            min-height:700px;
            width: calc(100% - 272px - 15px);
            max-width:  100%;
        }
    }
    @media (max-width: 900px) {
        .product-page__filter {
            display: none;
        }
        .product-page__items {
            width:  100%;
            max-width:  100%;
        }
    }
    @media (min-width: 900px) and (max-width: 1199px) {
        .product-page {
            &__filter {
                width: 234px;
            }
            &__items {
                width: calc(100% - 234px - 12px);
            }
        }

    }
    @media  (max-width: 700px) {
        .title {
            font-size: 20px;
            line-height: 24px;
        }
        .product-page {
            &__question {
                display: none;
            }
        }
    }

</style>
