<template>
    <div class="form__element">
        <div>Выберите магазин</div>
        <select v-model="storeIdLocal">
            <option value="null">Выберите магазин</option>
            <option
                    v-for="store in storesWithLinksCount"
                    :key="store.id"
                    :value="store.id"
            >
                {{ store.id }} {{ store.name }} {{ store.count }}
            </option>
        </select>
    </div>
</template>

<script>
    import {mapActions, mapState} from "vuex";

    export default {
        props:{
            storeId: {
                default: null
            },
            forPrice: {
                type: Boolean,
                default: false
            }
        },
        computed: {
            ...mapState('parsingLink', ['storesWithLinksCount']),
            storeIdLocal:{
                get() {
                    return this.storeId
                },
                set(value) {
                    this.$emit('update:storeId', value)
                }
            }
        },
        created() {
            this.loadStoresWithLinksCount(this.forPrice);
        },
        methods: {
            ...mapActions('parsingLink', ['loadStoresWithLinksCount'])
        }
    }
</script>

<style lang="scss" scoped>
   @import '../../../../css/admin/form';
   .option__not-empty {
       background: #84f4bd;
   }
</style>
