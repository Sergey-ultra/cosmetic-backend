<template>
    <div class="top-block">
        <div class="top-block__offers" v-if="receipts.length">
            <div class="receipts">
                <div class="receipts__inner">
                    <a href="#" class="receipts__item" v-for="(receipt,index) in receipts" :key="index">
                        {{ receipt.name }}
                    </a>
                </div>
            </div>
        </div>
        <div class="top-filters">
            <div class="top-filters__wrap">
                <select class="top-filters__item-sort select" v-model="sortLocal">
                    <option
                            v-for="option in sortOptions"
                            :key="option"
                            :value="option.value"
                    >
                        {{option.name}}
                    </option>
                </select>

                <div class="top-filters__item-view">
                    <div class="view-mobile">

                        <svg
                                v-if="viewMode === 'full'"
                                class="view-mode__item"
                                @click="setViewMode('compact')"

                                width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M2.90914 1.45455C2.10561 1.45455 1.45441 2.1058 1.45441 2.9094V4.36416C1.45441 5.16776 2.10561 5.81901 2.90914 5.81901H13C13.8035 5.81901 14.4547 5.16776 14.4547 4.36416V2.9094C14.4547 2.1058 13.8035 1.45455 13 1.45455H2.90914ZM0 2.9094C0 1.30248 1.30236 0 2.90914 0H13C14.6068 0 15.9091 1.30248 15.9091 2.9094V4.36416C15.9091 5.97108 14.6068 7.27356 13 7.27356H2.90914C1.30236 7.27356 0 5.97108 0 4.36416V2.9094ZM2.90914 10.181C2.10561 10.181 1.45441 10.8322 1.45441 11.6358V13.0906C1.45441 13.8942 2.10561 14.5455 2.90914 14.5455H13C13.8035 14.5455 14.4547 13.8942 14.4547 13.0906V11.6358C14.4547 10.8322 13.8035 10.181 13 10.181H2.90914ZM0 11.6358C0 10.0289 1.30236 8.72644 2.90914 8.72644H13C14.6068 8.72644 15.9091 10.0289 15.9091 11.6358V13.0906C15.9091 14.6975 14.6068 16 13 16H2.90914C1.30236 16 0 14.6975 0 13.0906V11.6358Z" fill="#333"/>
                        </svg>
                        <!--                    <fa icon="bars"></fa>-->
                        <fa
                                v-else
                                icon="th-large"
                                class="view-mode__item"
                                :class="{'view-mode__item-active': viewMode === 'compact'}"
                                @click="setViewMode('full')"
                        >
                        </fa>
                    </div>
                    <div class="view-mode">
                        <svg
                                class="view-mode__item"
                                :class="{'view-mode__item-active': viewMode === 'full'}"
                                @click="setViewMode('full')"

                                width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M2.90914 1.45455C2.10561 1.45455 1.45441 2.1058 1.45441 2.9094V4.36416C1.45441 5.16776 2.10561 5.81901 2.90914 5.81901H13C13.8035 5.81901 14.4547 5.16776 14.4547 4.36416V2.9094C14.4547 2.1058 13.8035 1.45455 13 1.45455H2.90914ZM0 2.9094C0 1.30248 1.30236 0 2.90914 0H13C14.6068 0 15.9091 1.30248 15.9091 2.9094V4.36416C15.9091 5.97108 14.6068 7.27356 13 7.27356H2.90914C1.30236 7.27356 0 5.97108 0 4.36416V2.9094ZM2.90914 10.181C2.10561 10.181 1.45441 10.8322 1.45441 11.6358V13.0906C1.45441 13.8942 2.10561 14.5455 2.90914 14.5455H13C13.8035 14.5455 14.4547 13.8942 14.4547 13.0906V11.6358C14.4547 10.8322 13.8035 10.181 13 10.181H2.90914ZM0 11.6358C0 10.0289 1.30236 8.72644 2.90914 8.72644H13C14.6068 8.72644 15.9091 10.0289 15.9091 11.6358V13.0906C15.9091 14.6975 14.6068 16 13 16H2.90914C1.30236 16 0 14.6975 0 13.0906V11.6358Z" fill="#333"/>
                        </svg>
                        <!--                    <fa icon="bars"></fa>-->
                        <fa
                                icon="th-large"
                                class="view-mode__item"
                                :class="{'view-mode__item-active': viewMode === 'compact'}"
                                @click="setViewMode('compact')"
                        >
                        </fa>
                    </div>
                </div>
                <div class="top-filters__item-filter" @click="showMobileFilter">
                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                         viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
                            <g>
                                <path d="M257.6,128c7.4-36.5,39.7-64,78.4-64s71,27.5,78.4,64H512v32h-97.6c-7.4,36.5-39.7,64-78.4,64s-71-27.5-78.4-64H0v-32
                                    H257.6z M336,192c26.5,0,48-21.5,48-48s-21.5-48-48-48s-48,21.5-48,48S309.5,192,336,192z"/>
                                <path d="M97.6,352c7.4-36.5,39.7-64,78.4-64s71,27.5,78.4,64H512v32H254.4c-7.4,36.5-39.7,64-78.4,64s-71-27.5-78.4-64H0v-32H97.6z
                                     M176,416c26.5,0,48-21.5,48-48s-21.5-48-48-48s-48,21.5-48,48S149.5,416,176,416z"/>
                            </g>
                    </svg>
                    <div class="top-filters__badge" v-if="pickedFilters.length">{{ pickedFilters.length }}</div>
                </div>
            </div>
            <div class="picked-wrap">
                <div v-if="pickedFilters.length" class="picked-filter picked-filter_all" @click="resetAllProductFilterQuery">
                    Сбросить фильтры
                    <span class="picked-filter-close">✕</span>
                </div>
                <div class="picked-filter"
                    v-for="picked in pickedFilters"
                     :key="picked.value"
                     @click="resetPartOfFilterQuery(picked.value)"
                >
                    {{ picked.name }}
                    <div class="picked-filter-close">
                        <div>✕</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <filterMobile
            v-if="isShowMobileFilter"
            v-model:isShowMobileFilter="isShowMobileFilter"
    />
</template>

<script setup>
    import filterMobile from "./filter-mobile";
    import {useProductStore} from "../../store/product";
    import {useFilterStore} from "../../store/filter";
    import {computed, reactive, ref} from "vue";
    import {storeToRefs} from "pinia";
    import {useRoute, useRouter} from "vue-router";


    let sortOptions = reactive([
        {name: 'По популярности', value: 'popular'},
        {name: 'Цена: Низкая-Большая', value: 'price-asc'},
        {name: 'Цена: Большая-Низкая', value: 'price-desc'},
        {name: 'По рейтингу', value: 'rating'},

    ]);

    let isShowMobileFilter = ref(false);

    const productStore = useProductStore();
    const filterStore = useFilterStore();
    const { filterOptions, viewMode, sortOption, filterOptionsKeys } = storeToRefs(productStore);
    const { receipts, filters } = storeToRefs(filterStore);

    const route = useRoute();
    const router = useRouter();

    const sortLocal = computed({
        get() {
            return sortOption.value;
        },
        set(value) {
            const query = {...route.query};
            if (value !== 'popular') {
                query.sort = value;
            } else {
                delete query.sort;
            }
            router.push({query});
        }
    });


    const pickedFilters = computed(() => {
        let pickedFilters = []

        if (filterOptions.value.min_price || filterOptions.value.max_price) {
            let picked = {value: 'price'}
            let name = 'Цена:'
            if (filterOptions.value.min_price) {
                name += ` от ${filterOptions.value.min_price}`
            }
            if (filterOptions.value.max_price) {
                name += ` до ${filterOptions.value.max_price}`
            }
            picked.name = name
            pickedFilters.push(picked)
        }
        const keys = filterOptionsKeys.value.filter(key => !['min_price', 'max_price'].includes(key));

        keys.forEach(key => {
            if (filterOptions.value[key].length > 0) {
                let picked = {value: key};

                let name = getTitle(key) + ' ';

                if (filterOptions.value[key].length === 1) {
                    if (['volumes', 'purposes'].includes(key)) {
                        name += filterOptions.value[key][0];

                    } else {
                        name += filters.value[getFilterKey(key)].length
                            ? filters.value[getFilterKey(key)].find(el => el.id === Number(filterOptions.value[key][0])).name
                            : '';

                    }

                } else {
                    name += `: ${filterOptions.value[key].length} знач.`;
                }

                picked.name = name;
                pickedFilters.push(picked);
            }
        })

        return pickedFilters;
    });

    const setViewMode = productStore.setViewMode;


    const showMobileFilter = () =>  isShowMobileFilter.value = true;

    const getTitle = value => {
        switch (value) {
            case 'category_ids':
                return 'Категории';
            case 'brand_ids':
                return 'Бренды';
            case 'volumes':
                return 'Объемы';
            case 'purposes':
                return 'Цели';
            case 'active_ingredients_group_ids':
                return 'Активные ингредиенты';
            case 'extract_ids':
                return 'Масла и экстракты';
            case 'country_ids':
                return 'Страны';
            default:
                return '';
        }
    };

    const getFilterKey = value => {
        switch (value) {
            case 'category_ids':
                return 'categories';
            case 'brand_ids':
                return 'brands';
            case 'volumes':
                return 'volumes';
            case 'purposes':
                return 'purposes';
            case 'active_ingredients_group_ids':
                return 'ingredients';
            case 'extract_ids':
                return 'extracts';
            case 'country_ids':
                return 'countries';
        }
    };

    const resetPartOfFilterQuery = field => {
        const query = {...route.query};
        if (field === 'price') {
            delete query.min_price;
            delete query.max_price;
        } else {
            delete query[field];
        }

        router.push({query});
    };

    const resetAllProductFilterQuery = () => {
        const query = {...route.query};
        filterOptionsKeys.value.forEach(key => delete query[key]);
        router.push({query});
    };
</script>

<style lang="scss" scoped>
    .select {
        border-radius: 8px;
        border: 1px solid transparent;
        outline: #000 none medium;
        overflow: visible;
        background-color: rgb(240, 242, 252);
        padding: 8px;

        &:hover {
            border-color: rgb(192, 201, 240);
            transition: border-color 0.3s ease 0s;
        }
    }

    .top-block {
        border-radius: 8px;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.16);
        background-color: #fff;
        margin-bottom: 8px;

        &__offers {
            width: 100%;
            border-bottom: 1px solid #eaeaea;
        }
    }




    .receipts {
        max-height: 32px;
        box-sizing: content-box;
        margin-bottom: 1em;
        overflow: hidden;
        padding: 1em 20px 0;
        &__item {
            background-color: #f9f9f9;
            border-radius: 15px;
            border: 1px solid #d8d8d8;
            display: inline-block;
            font-size: 14px;
            line-height: 30px;
            margin: 0 8px 0 0;
            padding: 0 12px;
            text-align: center;
            transition-duration: .3s;
            transition-property: color;
            text-decoration: none;
            cursor: pointer;
            color: #333;
            &:hover {
                color:#46bd87;
            }
        }
    }

    .top-filters {
        padding: 12px 0;
        &__wrap {
            padding: 0 20px;
            display:flex;
            align-items:center;
        }
        &__item {
            &-view {
                margin-left:auto;
            }
            &-filter {
                display: none;
            }
        }
    }

    .view-mobile {
        display: none;
    }
    .view-mode {
        display:flex;
        &__item {

            cursor:pointer;
            height:15px;
            width:22px;
            color:#8c8c8c;
            &:not(:last-child) {
                margin-right: 6px;
            }
            &-active {
                color: #333;
            }
        }
    }
    .picked-wrap {
        padding: 0 20px;
        display: flex;
        margin-top: 10px;
        margin-bottom: -10px;
        flex-wrap: wrap;
        width: 100%;
    }
    .picked-filter {
        white-space: nowrap;
        font-size: 13px;
        height: 30px;
        line-height: 24px;
        padding: 5px 8px 5px 12px;
        position: relative;
        margin: 0 10px 10px 0;
        display: flex;
        align-items: center;
        cursor: pointer;
        background: #ffb1ca;
        border-radius: 100px;
        color: #fff;
        border: 1px solid #f88cb6;
        &_all {
            background: #fff;
            border: 1px solid #8c8c8c;
            color: #333;
        }
        &-close {
            padding: 0 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            color: #fff;
            text-align: center;
            text-decoration: none;
            background-color: rgba(0, 0, 0, 0.14);
            font-size: 13px;
            line-height: 24px;
            margin-left: 8px;
            cursor: pointer;

            &:hover {
                background: #000;
            }
        }
    }


    @media (max-width: 900px) {
        .view-mode {
            display: none;
        }
        .view-mobile {
            display: flex;
        }
        .top-filters {
            &__item {
                &-view {
                    order:1;
                    margin-left:0;
                    margin-right: 20px;
                }
                &-sort {
                    order:2;
                }
                &-filter {
                    position: relative;
                    margin-left: auto;
                    cursor: pointer;
                    width: 20px;
                    height: 20px;
                    display:flex;
                    order:3;
                }
            }
            &__badge {
                position: absolute;
                left: 16px;
                top: -7px;
                height: 16px;
                line-height: 16px;
                min-width: 16px;
                padding: 0 5px;
                display: inline-flex;
                justify-content: center;
                align-items: center;
                border-radius: 50%;
                background-image: linear-gradient(to bottom, #ffd6e7, rgb(248, 140, 182));
                color: #fff;
                font-size: 12px;
                font-weight: bolder;
                text-align: center;

            }
        }
        .picked-wrap {
            flex-wrap: nowrap;
            overflow-y: hidden;
            overflow-x: auto;
        }
    }

    @media (max-width: 500px) {
        .receipts {
            display: none;
        }
    }
</style>