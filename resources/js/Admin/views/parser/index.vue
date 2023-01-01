<template>
    <div class="row">
        <div class="row__name">
            <input type="checkbox" v-model="isSelectAll">
            Выбрать все
        </div>
    </div>

    <div
            class="row"
            v-for="table in tables" :key="table"
    >
        <div class="row__name">
            <input type="checkbox" v-model="selectedTables" :value="table">
            {{ table }}
        </div>
        <div v-if="isParsing && selectedTables.includes(table)" class="row__loader">
            <loader v-if="getIsLoadingName(table)"/>
            <div v-else>
                x
            </div>
        </div>
    </div>


    <btn class="load-prices" @click="loadToDb">Скопировать из парсера</btn>
</template>

<script>
    import {mapActions,  mapState} from "vuex";
    import btn from "../../components/btn.vue";
    import loader from "../../components/loader.vue";

    export default {
        name: "parser",
        components: {
            btn,
            loader
        },
        data() {
            return {
                tables: [
                    'country',
                    'brand',
                    'category',
                    'product',
                    'sku',
                    'priceHistory',
                    'currentPrice',
                    'store',
                    'link',
                    'ingredient',
                    'ingredientProduct',
                ],
                selectedTables:[
                    'brand',
                    'category',
                    'country',
                    'link',
                    'product',
                    'sku',
                    'priceHistory',
                    'currentPrice',
                    'store',
                    'ingredient',
                    'ingredientProduct',
                ],
                isSelectAll: true,
                isParsing: false
            }
        },
        computed:{
            ...mapState('parser',[
                'isLoadingBrand',
                'isLoadingCategory',
                'isLoadingCountry',
                'isLoadingLink',
                'isLoadingProduct',
                'isLoadingSku',
                'isLoadingPriceHistory',
                'isLoadingCurrentPrice',
                'isLoadingStore',
                'isLoadingIngredient',
                'isLoadingIngredientProduct',
                'messageAfterLoadPricesToDb',
                'messageAfterLoadStoresToDb',
            ]),

        },
        watch:{
            isSelectAll(value) {
                this.selectedTables = value ? [...this.tables] : []
            },
        },
        methods:{
            ...mapActions('parser', [
                'loadBrand',
                'loadCategory',
                'loadCountry',
                'loadLink',
                'loadProduct',
                'loadSku',
                'loadPriceHistory',
                'loadCurrentPrice',
                'loadStore',
                'loadIngredient',
                'loadIngredientProduct',
            ]),
            capitalizeFirstLetter(string) {
                return string.charAt(0).toUpperCase() + string.slice(1);
            },
            getIsLoadingName(table) {
                let name = 'isLoading' + this.capitalizeFirstLetter(table)
                return this[name]
            },
            loadToDb() {
                this.isParsing = true
                this.selectedTables.forEach(el => {
                    let func = 'load' + this.capitalizeFirstLetter(el)
                    this[func]()
                })
            }
        }
    }
</script>

<style lang="scss" scoped>

    .row {
        height: 40px;
        display: flex;
        align-items: center;
        & div {
            padding: 0 10px;
        }
        &__name {
           min-width:300px;
        }
        &__load-images {
            white-space: nowrap;
        }
    }
    .load-prices {
        display:flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 80px;
    }
    .load-from-file {
        margin-right: 15px;
    }
    a {
        color:#fff;
        text-decoration: none;
    }
</style>
