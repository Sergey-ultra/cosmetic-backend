<template>
    <div>
        <div class="filters-top">
            <div class="input-search__catalog-filter">
                <input type="text" class="input-search__input-catalog-filter" placeholder="Поиск по категории">
                <div class="search-icon-box">
                    <fa class="search-icon" icon="search"></fa>
                </div>

            </div>
        </div>
        <div class="filter__wrapper" ref="filterMain">
            <div class="filter__list">

                <div class="filter__item">
                    <div class="collapse__link">
                        <fa class="collapse__icon" icon="angle-down"></fa>
                        <span>Цена</span>
                    </div>
                    <div class="collapse__content">
                        <div class="price__range">
                            <div class="price__input">
                                <input
                                        class="price__digit"
                                        :placeholder="'от ' + (filters.min_price ?? '')"
                                        type="number"
                                        v-model="localFilterParams.min_price"
                                        @input="setMinAndMaxPrices('min')"
                                        @change="showFilterSubmit($event)"
                                >
                            </div>
                            <div class="price__input">
                                <input
                                        class="price__digit"
                                        :placeholder="'до ' + (filters.max_price ?? '')"
                                        type="number"
                                        v-model="localFilterParams.max_price"
                                        @input="setMinAndMaxPrices('max')"
                                        @change="showFilterSubmit($event)"
                                >
                            </div>
                        </div>

<!--                       <price-range-->
<!--                           v-if="false"-->
<!--                           v-model:minPrice="localFilterParams.min_price"-->
<!--                           v-model:maxPrice="localFilterParams.max_price"-->
<!--                           @setMinAndMaxPrices="setMinAndMaxPrices"-->
<!--                           @showFilterSubmit="showFilterSubmit"-->
<!--                       />-->

                    </div>
                </div>

                <filterList
                        v-for="filterKey in ['categories', 'brands', 'ingredients', 'extracts', 'countries', 'volumes', 'purposes']"
                        :key="filterKey"
                        :filterKey="filterKey"
                        v-model:localFilterParams="localFilterParams[getLocalFilterListKey(filterKey)]"
                        @showFilterSubmit="showFilterSubmit"
                />


            </div>

            <div class="buttons">
                <div class="buttons__element">
                    <button class="buttons__btn" type="button" @click="setFilterQuery">Применить</button>
                </div>
                <div class="buttons__element">
                    <button class="buttons__btn" type="button" @click="resetFilterQuery">Сбросить</button>
                </div>
            </div>

            <div
                v-if="isShowFilterSubmit"
                class="filter__apply"
                :style="{ top:  topFilterSubmit + 'px' }"
                @click="setFilterQuery"
        >
            <span>Показать</span>
        </div>
        </div>
    </div>
</template>

<script setup>
    import filterList from './filter-list';
    import {onMounted, watch, ref, reactive} from "vue";
    import {storeToRefs} from "pinia";
    import {useFilterStore} from "../../store/filter";
    import {useProductStore} from "../../store/product";
    import {useRoute, useRouter} from "vue-router";

    const filterStore = useFilterStore();
    const productStore = useProductStore();
    const { isLoadingFilter, filters } = storeToRefs(filterStore);
    const { filterOptions } = storeToRefs(productStore);

    const route = useRoute();
    const router = useRouter();
    let localFilterParams = ref({
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

    let isShowFilterSubmit = ref(false);
    let topFilterSubmit = ref(0);
    let filterMain = ref(null);

    const getCoordsY = elem => {
        let box = elem.getBoundingClientRect();
        return {top: box.top, bottom: box.bottom};
    };

    const calculateYOfElement = element => {
        const elementCenter = getCoordsY(element).top + element.offsetHeight / 2
        const filterTop = getCoordsY(filterMain.value).top
        topFilterSubmit.value = elementCenter - filterTop
    };

    const showFilterSubmit = event => {
        event.stopImmediatePropagation();

        calculateYOfElement(event.target);
        isShowFilterSubmit.value = true;
        setTimeout(() => isShowFilterSubmit.value = false, 5000);
    };


    const setMinAndMaxPrices = (mode = 'min') => {
        if (Number(localFilterParams.max_price.value) - Number(localFilterParams.min_price.value) < 1000) {
            if (mode === 'min') {
                localFilterParams.min_price.value = Number(localFilterParams.max_price.value) - 1000
            } else {
                localFilterParams.max_price.value = Number(localFilterParams.min_price.value) + 1000
            }
        }
    };

    const resetLocalFilter = (value) => {
        if (value === 'min_price' || value === 'max_price') {
            localFilterParams.value[value] = "";
        } else {
            localFilterParams.value[value] = [];
        }

        isShowFilterSubmit.value = false
    };

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

    const setLocalFilterParams = () => {
        localFilterParams.value = {
            min_price: filterOptions.value.min_price,
            max_price: filterOptions.value.max_price,
            brand_ids: [...filterOptions.value.brand_ids],
            category_ids: [...filterOptions.value.category_ids],
            volumes: [...filterOptions.value.volumes],
            purposes: [...filterOptions.value.purposes],
            active_ingredients_group_ids: [...filterOptions.value.active_ingredients_group_ids],
            extract_ids: [...filterOptions.value.extract_ids],
            country_ids: [...filterOptions.value.country_ids],
        }
    };

    const setFilterQuery = () => {
        const query = Object.assign({...route.query}, { ...localFilterParams.value })

        if (!localFilterParams.value.min_price) {
            delete query.min_price
        }
        if (!localFilterParams.value.max_price) {
            delete query.max_price
        }

        delete query.page

        router.push({query})
        isShowFilterSubmit.value = false
    };

    const resetFilterQuery = () => {
        const query = Object.assign({...route.query}, {
            brand_ids: [],
            category_ids: [],
            volumes: [],
            purposes: [],
            active_ingredients_group_ids: [],
            extract_ids: [],
            country_ids: []
        });

        delete query.min_price;
        delete query.max_price;
        delete query.page;

        router.push({query});
        isShowFilterSubmit.value = false;
    };

    watch(
        filterOptions,
        () => setLocalFilterParams(),
        {deep: true}
    );

    onMounted(() => setLocalFilterParams());
</script>

<style lang="scss" scoped>

    .input-search__catalog-filter {
        position: relative;
        background-color: #fff;
        height: 40px;
        line-height: 40px;
        font-size: 14px;
        transition: background .3s;
        border-radius: 8px;
        box-shadow: 0 1px 2px 0 rgba(0,0,0,0.16);
        margin-bottom: 8px;
    }
    .input-search__input-catalog-filter {
        color: #333;
        background-color: #fff;
        padding: 0 50px 0 12px;
        width: 100%;
        height: 34px;
        border-radius: 8px;
        border: none;
        outline: none;
        transition: background .3s;
    }
    .filter {

        &__wrapper {
            box-shadow: 0 1px 2px 0 rgba(0,0,0,0.16);
            border-radius: 8px;
            background-color: #ffeaf2;
            position: relative;
        }
        &__list {
            overflow: hidden;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        &__iten {
            width: 100%;
        }
        &__apply {
            position: absolute;
            transform: translate(calc(100% - 11px), -50%);
            right: -10px;
            background-color: #ff9e55;
            height: 40px;
            width: 100px;
            border-radius: 8px;
            z-index:5;
            color: #fff;
            cursor: pointer;
            & span {
                width: 100%;
                height: 100%;
                display: block;
                text-align: center;
                font-size: 16px;
                line-height: 40px;
            }
            &:before {
                position: absolute;
                content:'';
                width: 0;
                height: 0;
                top: 50%;
                transform: translate(-100%,-50%);
                border: 11px solid transparent;
                border-right: 11px solid #ff9e55;
            }
        }
    }
    .buttons {
        padding-bottom: 15px;
        &__element {
            margin:15px 8px;
        }
        &__btn {
            border-radius: 8px;
            padding: 6px 15px;
            display: block;
            border: 1px solid #ccc;
            background-color: #fff;
            text-align: center;
            width: 100%;
            vertical-align: middle;
            -ms-touch-action: manipulation;
            touch-action: manipulation;
            cursor: pointer;
            transition: background-color .5s ease-in,border-color .5s ease-in;

            &:hover {
                color: #333;
                background-color: #e6e6e6;
                border-color: #adadad;
            }
        }
    }


    .collapse {
        &__link {
            display: flex;
            align-items:center;
            padding: 8px 24px 8px 13px;
            font-weight: bold;
            &:hover {
                background-color: #f0ffd6;
            }
        }
        &__content {
            padding-bottom: 10px;
            &-item {
                margin: 5px 15px 8px;
            }
            &-element {
                margin-left: 16px;
            }
        }
        &__icon {
            color: #afafaf;
            display: inline-flex;
            font-size: 20px;
            height: 20px;
            width: 20px;
            align-items:center;
            justify-content: center;
            margin: 5px 8px 5px 0;
            padding-left: 0;
            vertical-align: top;
            transform: rotate(0deg);
            transition: all .3s ease-in ;
            &-rotate {
                transform: rotate(180deg);
            }
        }
    }
    .input__search {
        margin: 5px 15px 8px;
        position: relative;
        border-radius: 8px;
        height: 40px;
        line-height: 40px;
        font-size: 14px;
        transition: background .3s;
        &-input {
            color: #333;
            background: #fff;
            padding: 0 50px 0 12px;
            height: 34px;
            width: 100%;
            border-radius: 8px;
            border: none;
            outline: none;
            transition: background .3s;
        }
        &-icon {
            color: #afafaf;
            position: absolute;
            right: 5px;
            top: 0;
            font-size: 16px;
            height: 100%;
            width: 40px;
            text-align: center;
            cursor: pointer;
        }
    }

    .price {
        &__input {
            position: relative;
            background: #fff;
            border: 1px solid #d9d9d9;
            border-radius: 8px;
            height: 40px;
            font-size: 14px;
            display: inline-block;
            margin: 5px;
            width: 43%;
            &:first-child {
                margin-left: 15px;
            }
        }
        &__digit {
            color: #333;
            padding-left: 12px;
            height: 95%;
            border-radius: 8px;
            border: none;
            outline: none;
            width: calc(100% - 12px);
        }
    }



    .select__element {
        &-first {
            box-shadow: 0 1px 0 0 #e5e5e5;
        }
    }
    .shadow {
        box-shadow: 0 5px 4px -1px rgba(0,0,0,.15);
    }

    .checkbox {
        display: block;
        position: relative;
        font-size: 14px;
        line-height: 24px;
        user-select: none;
        cursor: pointer;
        color: #333;
        padding: 6px 12px 6px 38px;
        transition: 0.3s;
        margin: 0;
        &:hover {
            background-color: #fff7da;
        }

        &__input{
            position: absolute;
            opacity: 0;
            top: 50%;
            height: 0;
            margin: 0 !important;
            &:checked + .checkbox__check{
                background-color: #42b983;
                border: none;
                &::after {
                    content: "";
                    display: block;
                    width: 7px;
                    height: 4px;
                    border-bottom: 2px solid #fff;
                    border-left: 2px solid #fff;
                    position: absolute;
                    top: 45%;
                    left: 51%;
                    transform: translate(-50%, -50%) rotate(-45deg);
                }
            }
        }
        &__check {
            content: "";
            display: block;
            width: 14px;
            height: 14px;
            border: 1px solid #ccc;
            border-radius: 2px;
            background-color: #fff;
            position: absolute;
            top: 50%;
            transform: translate(0, -50%);
            left: 12px;
        }
    }


    .search-icon-box {
        position: absolute;
        right: 5px;
        top: 0;
        height:100%;
        width: 40px;
        max-height: 40px;
        text-align: center;
        cursor: pointer;
    }
    .search-icon {
        color: #afafaf;
    }
    .filter__count {
        color: #a0a2a5;
    }
    .show-all {
        border-bottom: 1px dotted;
        color: #2c509a;
        padding-top: 5px;
        cursor: pointer;
        font-size: 13px;
    }
    .checkbox-list {
        max-height: 252px;
        overflow-x: hidden;
        overflow-y: auto;
    }
    .reset-filter {
        cursor: pointer;
        margin-right: 16px;
        padding-top: 5px;
        font-size: 13px;
        float: right;
        border-bottom: 1px dotted;
    }
</style>
