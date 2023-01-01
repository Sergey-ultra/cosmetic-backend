<template>
    <div class="price__parse">
        <div class="price__parse-element">
            <store-with-link-count
                    v-model:storeId="storeId"
            />

            <h4>Настройка тегов парсинга</h4>
            <form class="form__block" @input="setFormChangingToTrue">
                <div class="field__item">
                    <div class="tagName">тег цены</div>
                    <input class="web__tag" type="text" v-model="editedPriceTag">
                </div>
                <div class="form__element">
                    <button
                            class="button"
                            type="button"
                            :disabled="!(isFormChanging && storeId !== null)"
                            @click="saveOptions"
                    >
                        Сохранить настройки
                    </button>
                </div>
            </form>
        </div>
        <button
                type="button"
                class="button button__price"
                :disabled="isLoading"
                @click="parsePricesFromActualPriceParsingTable"
        >
            Спарсить цены всех магазинов

            <loader :color="'#fff'" class="loader" v-if="isLoading"/>
        </button>
    </div>
    <div class="table-block">
        <link-table
                :forPrice="true"
                :storeId="storeId"
                v-model:isReloadLinks="isReloadLinks"
                @getItemsByLinkIds="parsePriceByLinkIds"
        />
        <div v-show="isLoadingPrice" class="table__layer"></div>
    </div>
</template>
<script>
    import storeWithLinkCount from "./store-with-link-count.vue"
    import linkTable from "../link-table.vue"
    import loader from "../../../components/loader.vue"
    import {mapActions, mapState} from "vuex";

    export default {
        name: "price-parser",
        components:{
            storeWithLinkCount,
            loader,
            linkTable,
        },
        data() {
            return {
                isFormChanging:false,
                storeId: null,
                isReloadLinks: false,
                editedPriceTag:''
            }
        },
        computed:{
            ...mapState('priceOptions', ['priceTag']),
            ...mapState('priceParser', ['isLoading', 'isLoadingPrice']),
        },
        watch:{
            async storeId(value) {
                this.isFormChanging = false
                if (! ['null', undefined, ''].includes(value)) {
                    this.loadPriceOptions(value)
                }
            },
            priceTag(value) {
                this.editedPriceTag = value
            },
        },
        methods:{
            ...mapActions('priceOptions', ['loadPriceOptions', 'savePriceOptions']),
            ...mapActions('priceParser', ['parsePriceByLinkIds', 'parsePricesFromActualPriceParsingTable']),
            setFormChangingToTrue() {
                this.isFormChanging = true
            },
            saveOptions() {
                this.isFormChanging = false
                this.savePriceOptions({
                    store_id: this.storeId,
                    options: {
                        priceTag: this.editedPriceTag
                    }
                })
            },
        }
    }
</script>

<style lang="scss" scoped>
    .price__parse {
        display: flex;
        justify-content: space-between;
    }
    .form__group {
        display: flex;
        justify-content: space-between;
    }
    .form__element {
        margin-top: 15px;
    }
    .web__tag {
        width:500px;
    }
    button {
        min-width: 28px;
        padding: 0 20px;
        display: flex;
        justify-content: center;
        position: relative;
        align-items: center;
        border-radius: 4px;
        color: #fff;
        height: 28px;
        background: rgb(24, 103, 192) none repeat scroll 0% 0%;
        border: 1px solid rgb(24, 103, 192);
        &:before {
            background-color: currentColor;
            border-radius: inherit;
            bottom: 0;
            color: inherit;
            content: "";
            left: 0;
            opacity: 0;
            pointer-events: none;
            position: absolute;
            right: 0;
            top: 0;
            transition: opacity .2s cubic-bezier(.4,0,.6,1);
        }
        &:hover::before {
            opacity: .08;
        }
        &[disabled] {
            border: none;
            background-color: rgba(0,0,0,.12);
            color: rgba(0,0,0,.26);
        }
    }
    .table-block {
        margin-top: 100px;
        position:relative;
    }
    .table__layer {
        position: absolute;
        top:0;
        bottom:0;
        left:0;
        right:0;
        background-color: rgba(255, 255, 255, 0.81);
    }




    .button__price {
        height: 35px;
    }
    .loader {
        position: absolute;
        right: 0;
    }
</style>
