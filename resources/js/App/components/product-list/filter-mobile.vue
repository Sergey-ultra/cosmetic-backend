<template>
    <Teleport to="#app">
        <div class="filter">
            <div class="filter__header">
                <div>Фильтры</div>
                <button class="filter__close" @click="hideMobileFilter">
                    <span>✕</span>
                </button>
            </div>
            <div class="filter__inner">
                <div class="filter__item">
                    <div class="filter__title">Цена</div>
                    <div class="filter__prices">
                        <input
                                class="filter__price"
                                :placeholder="'от ' + filters.min_price"
                                type="text"
                                v-model="localFilterParams.min_price"
                        >
                        <input
                                class="filter__price"
                                :placeholder="'от ' + filters.max_price"
                                type="text"
                                v-model="localFilterParams.max_price"
                        >
                    </div>
                </div>
                <filterListMobile
                        v-for="filterKey in ['categories', 'brands', 'ingredients', 'extracts', 'countries', 'volumes', 'purposes']"
                        :key="filterKey"
                        :filterKey="filterKey"
                        v-model:localFilterParams="localFilterParams[getLocalFilterListKey(filterKey)]"
                        @hideMobileFilter="hideMobileFilter"
                />

            </div>
            <div class="apply__wrapper" v-if="!isEmptyMobileFilter || !isEmptyFilterOptions">
                <div class="apply__btn" @click="setFilterQuery">Применить</div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
    import filterListMobile from './filter-list-mobile';
    import {defineProps, defineEmits, ref, reactive, computed, onMounted, watch} from "vue";
    import {storeToRefs} from "pinia";
    import {useFilterStore} from "../../store/filter";
    import {useProductStore} from "../../store/product";
    import {useRoute, useRouter} from "vue-router";


    const localFilterParams = reactive({
        min_price: "",
        max_price: "",
        brand_ids: [],
        category_ids: [],
        volumes: [],
        purposes: [],
        active_ingredients_group_ids: [],
        extract_ids: [],
        country_ids: []
    });

    const route = useRoute();
    const router = useRouter();
    const emit = defineEmits(['update:isShowMobileFilter']);
    const props = defineProps({
        isShowMobileFilter: {
            type: Boolean,
            default: false
        }
    });

    const filterStore = useFilterStore();
    const productStore = useProductStore();
    const { isLoadingFilter, filters } = storeToRefs(filterStore);
    const { filterOptions, isEmptyFilterOptions } = storeToRefs(productStore);

    let isEmptyMobileFilter = computed(() => {
        for (let [key, value] of Object.entries(localFilterParams.value)) {
            if (key !== 'min_price' && key !== 'max_price' && value.length) {
                return false;
            } else if ((key === 'min_price' || key === 'max_price') && value !== "") {
                return false;
            }
        }
        return true;
    });

    watch(
        filterOptions,
        async  value => setLocalFilterParams(value),
        {deep: true}
    );

    onMounted(() => setLocalFilterParams(filterOptions.value));


    const hideMobileFilter = () => emit('update:isShowMobileFilter', false);

    const getLocalFilterListKey = value => {
        switch (value) {
            case 'categories':
                return 'category_ids';
            case 'brands':
                return 'brand_ids';
            case 'volumes':
                return 'volumes';
            case 'purposes':
                return 'purposes';
            case 'ingredients':
                return 'active_ingredients_group_ids';
            case 'extracts':
                return 'extract_ids';
            case 'countries':
                return 'country_ids';
        }
    };

    const setLocalFilterParams = value => {
        localFilterParams.value = {
            min_price: value.min_price,
            max_price: value.max_price,
            brand_ids: [...value.brand_ids],
            category_ids: [...value.category_ids],
            volumes: [...value.volumes],
            purposes: [...value.purposes],
            active_ingredients_group_ids: [...value.active_ingredients_group_ids],
            extract_ids: [...value.extract_ids],
            country_ids: [...value.country_ids],
        }
    };

    const setFilterQuery = () => {
        const query = Object.assign({...route.query}, {...localFilterParams.value});

        if (!localFilterParams.value.min_price) {
            delete query.min_price;
        }
        if (!localFilterParams.value.max_price) {
            delete query.max_price;
        }

        router.push({query});
        emit('update:isShowMobileFilter', false);
    };

</script>

<style lang="scss" scoped>
    .filter {
        background-color: #F8F8F8;
        display: block;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 201;
        width: 100%;
        height: 100%;
        overflow: hidden;
        outline: 0;

        &__header {
            background-color: #fff;
            padding: 10px;
            display:flex;
            align-items:center;
            justify-content: center;
            position:relative;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.14);
        }
        &__close {
            position: absolute;
            right: 15px;
            cursor: pointer;
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
            color: #000;
            text-shadow: 0 1px 0 #fff;
            opacity: .5;
            padding: 0;
            border-style: none;
            background-color: transparent;
        }
        &__inner {
            margin-top:15px;
            background-color: #fff;
        }
        &__item {
            padding: 15px;

        }
        &__title {
            font-weight: bold;
        }
        &__prices {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
            width: 100%;
        }
        &__price {
            padding: 0 15px;
            width: calc(50% - 20px);
            height: 38px;
            border-radius: 6px;
            border-width: 1px;
            border-color:#999999;
        }
    }
    .apply {
        &__wrapper {
            background-color: #fff;
            position: absolute;
            bottom: 0;
            width:100%;
            height: 60px;
            display:flex;
            align-items: center;
            justify-content: center;
        }

        &__btn {
            height: 40px;
            line-height:40px;
            width:100%;
            font-size: 14px;
            margin: 9px 10px;
            background-image: linear-gradient(to bottom, #ffd6e7, #f88cb6);
            border-radius: 4px;
            display: inline-block;
            text-align: center;
            color: #222;
            cursor: pointer;
            transition: color 0.12s ease-out;
            text-decoration: none;
            outline: 0;
            touch-action: manipulation;
        }
    }
</style>