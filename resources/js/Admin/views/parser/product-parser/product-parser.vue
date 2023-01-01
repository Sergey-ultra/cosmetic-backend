<template>
    <form name="parser" ref="parser">
        <div class="form__group">
            <store-with-unparsed-links
                    v-model:storeId="storeId"
            />
            <div class="form__element">
                <label>
                    <input type="checkbox" v-model="isSelectBrand">
                    Выбрать бренд <span style="fontSize:11px">(если на странице товары одного бренда и сам бренд не указан)</span>
                </label>
                <select v-model="brandId" v-if="isSelectBrand" >
                    <option value="null">Выберите бренд</option>
                    <option
                            v-for="brand in allBrands"
                            :key="brand.id"
                            :value="brand.id"
                    >
                        {{ brand.name }}
                    </option>
                </select>
            </div>
        </div>

        <div class="form__group">
            <button type="button" @click="toggleIsShowProductOptions" class="button button-settings">
                <svg class="form__icon" viewBox="0 0 24 24"><path d="M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8zm0 6a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"></path><path d="m22.215 7.759-1.427-2.483a1.398 1.398 0 0 0-1.755-.591l-2.22.93-1.69-.97-.307-2.422A1.403 1.403 0 0 0 13.426 1h-2.853a1.403 1.403 0 0 0-1.39 1.224L8.88 4.622l-1.72.982-2.191-.92a1.4 1.4 0 0 0-1.756.593L1.787 7.756a1.403 1.403 0 0 0 .364 1.814l1.855 1.41.003 2.038-1.859 1.413a1.402 1.402 0 0 0-.365 1.81l1.427 2.482a1.398 1.398 0 0 0 1.754.592l2.22-.93 1.69.97.308 2.421A1.404 1.404 0 0 0 10.574 23h2.853a1.403 1.403 0 0 0 1.39-1.224l.304-2.398 1.72-.982 2.192.92a1.402 1.402 0 0 0 1.755-.593l1.425-2.479a1.401 1.401 0 0 0-.365-1.814l-1.854-1.41-.002-2.038L21.85 9.57a1.402 1.402 0 0 0 .365-1.81zm-4.226 2.233.007 4.023 2.222 1.687-.9 1.565-2.613-1.097-3.443 1.966L12.898 21h-1.796l-.367-2.886-3.412-1.956-2.641 1.109-.9-1.565 2.229-1.694-.007-4.02-2.222-1.69.9-1.565L7.294 7.83l3.444-1.966L11.102 3h1.796l.367 2.886 3.412 1.956 2.641-1.109.9 1.565-2.229 1.694z"></path></svg>
                <span>{{ isShowProductOptions ? 'Скрыть настройки' : 'Показать настройки'}}</span>
            </button>
        </div>



        <product-options-form
                v-if="isShowProductOptions"
                :storeId="storeId"
        />

    </form>




    <div class="table-block">
        <link-table
                :storeId="storeId"
                @getItemsByLinkIds="parseByLinkIds"
                v-model:isReloadLinks="isReloadLinksLocal"

        >
            <template v-slot:buttons>
                <label>
                    Загрузить в базу данных
                    <input type="checkbox" v-model="isLoadToDb">
                </label>
                <label>
                    Показывать результаты в modal
                    <input type="checkbox" v-model="isOpenPreviewAfterParsingLocal">
                </label>
            </template>
        </link-table>
        <div v-show="isParsingProduct" class="table__layer"></div>
    </div>


    <preview-modal
            v-if="isShowPreviewLocal"
            v-model:isShowForm="isShowPreviewLocal"
    />

</template>

<script>
    import {mapActions, mapMutations, mapState} from "vuex";
    import productOptionsForm from "./product-options-form.vue"
    import storeWithUnparsedLinks from "./store-with-unparsed-links.vue"
    import linkTable from "../link-table.vue"
    import previewModal from "./preview-modal.vue"

    export default {
        name: "product-parser",
        components: {
            storeWithUnparsedLinks,
            linkTable,
            previewModal,
            productOptionsForm
        },
        data() {
            return {
                isSelectBrand: false,
                brandId:null,
                storeId: null,
                isLoadToDb: false,
                isShowProductOptions: false
            }
        },
        computed: {
            ...mapState('brand', ['allBrands']),
            ...mapState('productParser', ['isParsingProduct', 'isReloadLinks', 'preview', 'isShowPreview', 'isOpenPreviewAfterParsing']),
            isReloadLinksLocal:{
                get() {
                    return this.isReloadLinks
                },
                set(value) {
                    this.setIsReloadLinks(value)
                }
            },
            isShowPreviewLocal: {
                get() {
                    return this.isShowPreview
                },
                set(value) {
                    this.setIsShowPreview(value)
                }
            },
            isOpenPreviewAfterParsingLocal: {
                get() {
                    return this.isOpenPreviewAfterParsing
                },
                set(value) {
                    this.setIsOpenPreviewAfterParsing(value)
                }
            }
        },
        watch: {
            isSelectBrand(value) {
                if (value && !this.allBrands.length) {
                    this.loadAllBrands()
                }
            },
        },
        created() {

        },
        methods: {
            ...mapActions('brand', ['loadAllBrands']),
            ...mapActions('productParser', ['parseProductByLinkIds']),
            ...mapMutations('productParser', ['setIsReloadLinks', 'setIsShowPreview', 'setIsOpenPreviewAfterParsing']),
            parseByLinkIds(ids) {
                this.parseProductByLinkIds({
                    linkIds: ids,
                    brandId: this.brandId,
                    storeId: this.storeId,
                    isLoadToDb: this.isLoadToDb
                })

                if (this.isLoadToDb) {
                    this.isLoadToDb =  false
                }
            },
            toggleIsShowProductOptions() {
                this.isShowProductOptions = !this.isShowProductOptions
            }
        }
    }
</script>

<style scoped lang="scss">
    select option {
        display: flex;
        justify-content: space-between;
        width: 200px;

        & div {
            padding: 0 10px;
        }
    }
    .option__not-empty {
        background: #84f4bd;
    }
    .button {
        min-width: 28px;
        padding: 0 12.4px;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 4px;
        color: #fff;
        height: 28px;
        background: rgb(24, 103, 192) none repeat scroll 0% 0%;
        border: 1px solid rgb(24, 103, 192);
        &[disabled] {
            background: #ebeff4;
        }
        &-settings {
            margin-top: 20px;
            margin-left: auto;
        }
    }



    label {
        cursor:pointer;
        user-select: none;
        &:not(:last-child) {
            margin-right: 20px;
        }
    }

    .target__url {
        width:500px;
    }
    .parser {
        display: flex;
        flex-direction:column;
        &__settings {
            margin-top: 25px;
            display:flex;
            justify-content: space-between;
        }
    }
    .form {
        &__group {
            display: flex;
            justify-content: space-between;
        }
        &__icon {
            fill: #fff;
            margin-right: 10px;
            width: 20px;
            height: 20px;
        }
        &__element {
            margin-top: 15px;
        }
        &__block {
            background-color: rgba(0, 0, 0, 0.08);
            border-radius:5px;
            border: 1px solid rgba(0,0,0,0.55);
            margin:29px 0;
            box-shadow: 0px 1px 4px rgba(0,0,0,0.15);
            padding:20px;
        }
    }

    .field {
        display:flex;
        align-items:flex-end;
        &__row {
            display:flex;
            margin-top:10px;

        }
        &__item {
            &:not(last-child) {
                margin-right:15px;
            }
            &-img {
                min-width:200px;

            }
            &-tag {
                width:400px;
            }
            &-number {
                width:150px;
            }
            &-is-required {
                width: 40px;
            }
        }
    }

    .svg-loader{
        height: 100%;
        display:flex;
        position: relative;
        align-content: space-around;
        justify-content: center;
    }
    .loader-svg{
        position: absolute;
        left: 0; right: 0; top: 0; bottom: 0;
        fill: none;
        stroke-width: 5px;
        stroke-linecap: round;
        stroke: rgb(64, 0, 148);
    }
    .loader-svg.bg{
        stroke-width: 8px;
        stroke: rgb(207, 205, 245);
    }




    .add__field,
    .delete__field {
        display:flex;
        align-items:center;
        justify-content: center;
        margin-left:15px;
        padding: 0 10px;
        background-color: #fff;
        border:none;
        height:25px;
        box-shadow: 1px 0 0 #f3c8d2, -1px 0 0 #f3c8d2, 0 -1px 0 rgba(243,200,210,0.5), 0 4px 7px rgba(243,200,210,0.4), 0 1px 1px #f3c8d2;
    }

    .table-block {
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
</style>
