<template>
    <div class="form__element">
        <div>Выберите магазин</div>
        <select v-model="storeIdLocal">
            <option value="null">Выберите магазин</option>
            <option
                    v-for="store in availableStoresWithUnparsedLinks"
                    :key="store.id"
                    :value="store.id"
                    :class="{'option__not-empty': store.countBeforeEnd > 0}"
            >
                {{ store.id }} {{ store.name }} {{ store.countBeforeEnd }}
            </option>
        </select>
    </div>
</template>

<script>
    import {mapActions, mapState} from "vuex";
    export default {
        name: "store-with-unparsed-links",
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
            ...mapState('parsingLink', ['availableStoresWithUnparsedLinks']),
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
            this.loadStoresList()
        },
        methods:{
            ...mapActions('parsingLink', ['loadStoresList'])

        }
    }
</script>

<style lang="scss" scoped>
    .form__element {
        margin-top: 15px;
    }
    .option__not-empty {
        background: #84f4bd;
    }
</style>