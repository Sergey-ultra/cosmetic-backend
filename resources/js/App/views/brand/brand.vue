<template>
    <div class="product-page__title">
        <h1 class="title">{{ currentBrand ? currentBrand.name : '' }}</h1>
        <span class="product-page__count">{{  totalCount }} товаров</span>
<!--        <img :src="currentBrand.image" :alt="currentBrand.image">-->
    </div>




    <div class="product-page__wrapper">
        <filter-block class="product-page__filter"/>
        <product-list class="product-page__items"/>
    </div>

    <viewed-products/>
</template>

<script setup>
    import filterBlock from "../../components/filterBlock"
    import productList from '../../components/product-list'
    import viewedProducts from '../../components/viewed-products'
    import { storeToRefs } from "pinia";
    import {useRoute} from "vue-router";
    import { watch,  onMounted } from 'vue';
    import {useBrandStore} from "../../store/brand";
    import {useProductStore} from "../../store/product";
    import {useFilterStore} from "../../store/filter";

    const brandStore = useBrandStore();
    const productStore = useProductStore();
    const filterStore = useFilterStore();
    const { currentBrand, isLoadingCurrentBrand } = storeToRefs(brandStore);
    const { totalCount, filterOptions } = storeToRefs(productStore);

    const route =  useRoute();

    const loadProductsByBrandCode = () => {
        productStore.loadProductsWithPagination();
        filterStore.loadFilters();
    };
    const setTitle = name => {
        if (name) {
            document.title = route.meta.title + ' ' + name +
                ' Smart-Beautiful - агрегатор цен косметических товаров';
        }
    }

    watch(
        currentBrand,
        (value) => {
            if (value) {
                filterStore.loadReceipts()
                setTitle(value.name)
            }
        }
    );
    watch(
        () => ({params: route.params, query: route.query}),
        (value, oldValue) => {
            if (value.params.brand_code) {
                if (value.params.brand_code !== oldValue.params.brand_code) {
                    productStore.setProductLoadingMode({brand_code: value.params.brand_code})
                    brandStore.loadBrandByCode(value.params.brand_code)
                }
                productStore.setProductParamsByProductQuery(value.query)
                loadProductsByBrandCode()
            }
        },
        { deep: true }
    );

    onMounted(() => {
        if (route.params.brand_code) {
            productStore.setProductLoadingMode({ brand_code: route.params.brand_code })
            if (currentBrand) {
                setTitle(currentBrand.name)
            }
            brandStore.loadBrandByCode(route.params.brand_code)
            productStore.setProductParamsByProductQuery(route.query)
            loadProductsByBrandCode(route.params.brand_code)
        }
    });
</script>

<style lang="scss" scoped>
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
        &__title {
            margin: 40px 0 20px;
        }

        &__count {
            font-weight: bold;
            letter-spacing: .2px;
            font-size: 14px;
            display: inline-block;
            vertical-align: middle;
        }

        &__wrapper {
            display:flex;
            justify-content: space-between;
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
        .product-page__filter {
            width: 234px;
        }
        .product-page__items {
            width: calc(100% - 234px - 12px);
        }
    }
</style>
