<template>
    <div>
        <div>
            <span>Максимальное количество ссылок на один магазин</span>
            {{ maxLinkCountPerStore }}
        </div>
        <div>
            <span>Минимальнон количество запрос в магазин в час</span>
            {{ minHourLinkCount }}
        </div>
        <div>
            <span>Максимальное количество запрос в магазин в час</span>
            {{ maxHourLinkCount }}
        </div>
        <buttonComponent
            class="button__price"
            :isLoading="isLoading"
            :disabled="isLoading"
            @click="parsePricesFromActualPriceParsingTable"
        >
            Спарсить цены всех магазинов
        </buttonComponent>
    </div>
</template>

<script>
    import buttonComponent from "../../../components/button-component.vue"
    import {mapActions, mapState} from "vuex";
    export default {
        components:{
            buttonComponent,
        },
        computed:{
            ...mapState('priceParser', ['isLoading', 'maxLinkCountPerStore', 'minHourLinkCount', 'maxHourLinkCount']),
        },
        created() {
            this.getMaxLinkCountPerStore();
            this.getMinHourCount();
            this.getMaxHourCount();
        },
        methods:{
            ...mapActions('priceParser', ['parsePricesFromActualPriceParsingTable', 'getMaxLinkCountPerStore',
                'setMinHourCount', 'setMaxHourCount', 'getMinHourCount', 'getMaxHourCount']),
        }
    }
</script>

<style scoped lang="scss">
    .button__price {
        height: 35px;
    }
</style>
