<template>
    <div class="title-main">
        <h2 class="title">{{  $route.meta.title  }}</h2>
    </div>
    <div class="brands">
        <div v-if="isLoadingAllBrands"></div>
        <div v-else class="brands__content">
            <div class="filter">
                <select class="filter__item select filter__select" v-model="country">
                    <option value="null">Все страны</option>
                    <option v-for="country in countries" :key="country" :value="country">
                        {{ country }}
                    </option>
                </select>
                <input class="filter__item input filter__input" type="text" placeholder="Поиск по бренду" v-model.trim="search">
                <button class="filter__item button filter__button" @click="searchReset">Сброс</button>
            </div>
            <div
                    class="brands__list"
                    v-for="(letter, index) in handledBrands"
                    :key="index"
            >
                <div class="brands__title">{{ letter.letter }}</div>
                <div
                        class="brands__column"
                        :style="columnStyle"
                        v-for="(brandBlock, brandBlockIndex) in letter.brands"
                        :key="brandBlockIndex"
                >

                    <div
                            class="brands__item"
                            v-for="brand in brandBlock"
                            :key="brand.id"
                    >

                        <router-link :to="{ name: 'brand', params: { brand_code: brand.code }}">
                             {{ brand.name }}
                            <span>{{ brand.country }}</span>
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script setup>
    import { ref, computed, onMounted } from 'vue';
    import { storeToRefs } from "pinia";
    import {useBrandStore} from "../../store/brand";

    const country = ref('null');
    const search = ref('');
    const searchReset = () => {
        country.value = 'null';
        search.value = '';
    };

    const brandStore = useBrandStore();
    const loadAllBrand = brandStore.loadAllBrand;
    const { allBrands, isLoadingAllBrands, countries } = storeToRefs(brandStore);

    const spliceIntoChunks = (arr, countOfChunks) => {
        let res = [];
        const chunkSize = Math.ceil(arr.length / countOfChunks)
        for (let i = 0; i < countOfChunks; i++) {
            const chunk = arr.slice(chunkSize * i, chunkSize * (i + 1));
            res.push(chunk);
        }
        return res;
    }

    let filteredBrands = computed(() => {
        if (country.value !== 'null' || search.value !== '') {
            return allBrands.value.map(el => {
                let brands = el.brands

                if (country.value !== 'null') {
                    brands = brands.filter(brand => brand.country === country.value)
                }

                if (search.value !== '') {
                    brands = brands.filter(brand => brand.name.toLowerCase().includes(search.value.toLowerCase()))
                }

                return {
                    letter: el.letter,
                    brands: brands
                }
            }).filter(el => el.brands.length > 0)
        }
        return allBrands.value;
    });

    const chunksCount = computed(() => {
        let chunksCount = 3
        const width = document.documentElement.clientWidth
        if (width < 600 && width > 401) {
            chunksCount = 2
        }
        if (width < 400) {
            chunksCount = 1
        }
        return chunksCount
    });

    const handledBrands = computed(() => {
        let res = [];
        filteredBrands.value.forEach(el => {
            res.push({
                letter: el.letter,
                brands: spliceIntoChunks(el.brands, chunksCount.value)
            });
        });

        return res;
    });

    const columnStyle = computed(() => {
        return {
            width: `calc((100% - 16%) / ${chunksCount.value })`
        }
    });

    onMounted(() => {
        if (!allBrands.value.length) {
            loadAllBrand();
        }
    });
</script>

<style lang="scss" scoped>
    .title-main {
        margin: 30px 0 20px;
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
    .input {
        width: 100%;
        outline: #000 none medium;
        overflow: visible;
        transition: background-color 0.3s ease 0s, border-color 0.3s ease 0s;
        border: 1px solid transparent;
        border-radius: 8px;
        padding: 8px;
        background-color: rgb(240, 242, 252);
        &:hover {
            border-color: rgb(192, 201, 240);
            transition: border-color 0.3s ease 0s;
        }
        &:focus {
            background-color: white;
            border-color: rgb(59, 87, 208);
            transition: background-color 0.3s ease 0s, border-color 0.3s ease 0s;
        }
    }

    .button {
        background-color: #e8e8e8;
        border: none;
        color: #333;
        border-radius: 8px;
        transition: background-color 0.3s ease 0s, border-color 0.3s ease 0s;
        padding: 8px 30px;
        &:hover {
            background-color: #dcdcdc;
        }
    }

    .filter {
        display: flex;
        padding: 20px;
        flex-wrap: wrap;
        justify-content: flex-end;
        &__item {
            margin-bottom: 8px;
            width: 145px;
            &:not(:first-child) {
                margin-left: 15px;
            }
        }

        &__select,
        &__input {
            min-width:200px;
        }
        &__button {
            font-weight:bold;
        }
    }
    .brands {
        display: block;
        margin:8px 0;
        &__content {
            border-radius: 8px;
            box-shadow: 0 1px 2px 0 rgba(0,0,0,0.16);
            background-color: #fff;
        }

        &__list {
            display: flex;
            justify-content: space-between;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin-bottom: 15px;
        }
        &__title {
            font-size: 70px;
            line-height: 85%;
            color: #5cbf1a;
            width: 16%;
        }
        &__column {
            padding-right: 20px;
        }
        &__item {
            line-height: 26px;
            & a {
                font-size: 17px;
                text-decoration: none;
            }
            & span {
                color: #9fa3a7;
            }

        }
    }
    @media (max-width: 420px) {
        .filter {

            &__item {
                margin-top: 15px;

            }
        }
    }
</style>
