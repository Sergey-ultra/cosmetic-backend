<template>
    <div  class="product-page__title">
        <h2 class="title">
            {{ $route.query.search ? `Поиск по "${ $route.query.search }"` : 'Поисковое слово не выбрано'}}
        </h2>
        <span v-if="$route.query.search" class="product-page__count">{{ totalCount }} товаров</span>
    </div>


    <div v-if="$route.query.search" class="product-page__wrapper">
        <filter-block class="product-page__filter"/>
        <product-list class="product-page__items"/>
    </div>

    <viewed-products/>
</template>

<script setup>
    import filterBlock from "../../components/filterBlock"
    import productList from '../../components/product-list'
    import viewedProducts from '../../components/viewed-products'
    import { onMounted,  watch, ref } from 'vue';
    import { storeToRefs } from "pinia";
    import {useProductStore} from "../../store/product";
    import {useFilterStore} from "../../store/filter";
    import {useRoute} from "vue-router";

    const productStore = useProductStore();
    const filterStore = useFilterStore();
    const { totalCount, filterOptions } = storeToRefs(productStore);

    const route = useRoute();

    const isChangeSearchString = ref(false);

    const searchByString = () => {
        productStore.loadProductsWithPagination();
        filterStore.loadFilters();
    };
    const setTitle = name => {
        if (name) {
            document.title = `Поиск по ${name} Smart-Beautiful - агрегатор цен косметических товаров`;
        }
    };

    watch(
        () => ({ params: route.params, query: route.query }),
        (value, oldValue) => {
            if (![undefined, null].includes(value.query.search)) {

                if (value.query.search !== oldValue.query.search) {
                    setTitle(value.query.search);
                    productStore.setProductLoadingMode({search: value.query.search});
                }
                productStore.setProductParamsByProductQuery(value.query);
                searchByString();
            }
        },
        {deep: true}
    );

    onMounted(() => {
        if (![undefined, null].includes(route.query.search)) {
            setTitle(route.query.search);
            productStore.setProductLoadingMode({search: route.query.search});
            productStore.setProductParamsByProductQuery(route.query);
            searchByString();
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
            margin: 30px 0 20px;
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
            align-items: flex-start;
            position: relative;
        }


        &__filter {
            z-index:5;
            position: relative;
            width: 272px;
            display: block;
            min-height:300px;
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