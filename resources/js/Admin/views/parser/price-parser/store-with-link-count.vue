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
        name: "store-with-link-count",
        data() {
            return {

            }
        },
        props:{
            storeId: {
                default: null
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
            this.loadStoresWithLinksCount()
        },
        methods: {
            ...mapActions('parsingLink', ['loadStoresWithLinksCount'])
        }
    }
</script>

<style scoped>
    .form__element {
        margin-top: 15px;
    }
</style>