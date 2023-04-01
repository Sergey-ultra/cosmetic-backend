<template>
    <div class="price">
        <div>
            <store-with-link-count
                v-model:storeId="storeId"
                :forPrice="true"
            />

            <h4>Настройка тегов парсинга</h4>
            <form class="form" @input="setFormChangingToTrue">
                <div class="form__group">
                    <div class="tagName">тег цены</div>
                    <input class="web__tag" type="text" v-model="editedPriceTag">
                </div>
                <div class="form__element">
                    <buttonComponent
                        :size="'small'"
                        :disabled="!(isFormChanging && storeId !== 'null')"
                        @click="saveOptions"
                    >
                        <svg class="form__icon" viewBox="0 0 24 24">
                            <path d="M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8zm0 6a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"></path>
                            <path d="m22.215 7.759-1.427-2.483a1.398 1.398 0 0 0-1.755-.591l-2.22.93-1.69-.97-.307-2.422A1.403 1.403 0 0 0 13.426 1h-2.853a1.403 1.403 0 0 0-1.39 1.224L8.88 4.622l-1.72.982-2.191-.92a1.4 1.4 0 0 0-1.756.593L1.787 7.756a1.403 1.403 0 0 0 .364 1.814l1.855 1.41.003 2.038-1.859 1.413a1.402 1.402 0 0 0-.365 1.81l1.427 2.482a1.398 1.398 0 0 0 1.754.592l2.22-.93 1.69.97.308 2.421A1.404 1.404 0 0 0 10.574 23h2.853a1.403 1.403 0 0 0 1.39-1.224l.304-2.398 1.72-.982 2.192.92a1.402 1.402 0 0 0 1.755-.593l1.425-2.479a1.401 1.401 0 0 0-.365-1.814l-1.854-1.41-.002-2.038L21.85 9.57a1.402 1.402 0 0 0 .365-1.81zm-4.226 2.233.007 4.023 2.222 1.687-.9 1.565-2.613-1.097-3.443 1.966L12.898 21h-1.796l-.367-2.886-3.412-1.956-2.641 1.109-.9-1.565 2.229-1.694-.007-4.02-2.222-1.69.9-1.565L7.294 7.83l3.444-1.966L11.102 3h1.796l.367 2.886 3.412 1.956 2.641-1.109.9 1.565-2.229 1.694z"></path>
                        </svg>
                        <span>Сохранить настройки</span>
                    </buttonComponent>
                </div>
            </form>
        </div>
    </div>
    <div class="table">
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
    import storeWithLinkCount from "../../../components/parsing/store-with-link-count.vue"
    import linkTable from "../../../components/parsing/link-table.vue"
    import buttonComponent from "../../../components/button-component.vue"
    import {mapActions, mapMutations, mapState} from "vuex";

    export default {
        name: "price-parser",
        components:{
            storeWithLinkCount,
            buttonComponent,
            linkTable
        },
        data() {
            return {
                isFormChanging:false,
                storeId: 'null',
                isReloadLinks: false,
                editedPriceTag:''
            }
        },
        computed:{
            ...mapState('priceOptions', ['priceTag']),
            ...mapState('priceParser', ['isLoadingPrice']),
        },
        watch:{
            async storeId(value) {
                this.isFormChanging = false
                if (! ['null', undefined, ''].includes(value)) {
                    await this.loadPriceOptions(value);
                } else {
                    this.setPriceOptions('');
                }
            },
            priceTag(value) {
                this.editedPriceTag = value;
            },
        },
        methods:{
            ...mapActions('priceOptions', ['loadPriceOptions', 'savePriceOptions']),
            ...mapMutations('priceOptions', ['setPriceOptions']),
            ...mapActions('priceParser', ['parsePriceByLinkIds']),
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
   @import '../../../../../css/admin/form';
    .price {
        display: flex;
        justify-content: space-between;
    }

    .table {
        margin-top: 100px;
        position:relative;
        &__layer {
            position: absolute;
            top:0;
            bottom:0;
            left:0;
            right:0;
            background-color: rgba(255, 255, 255, 0.81);
        }
    }
    .web__tag {
        width:500px;
    }
</style>
