<template>
    <div class="select" @click="!isLoadingAllStores ? toggleShowOptions() : ''">
        <span
            v-if="selectedStore === null"
            class="select__inner"
            :class="{'select__inner-disabled': isLoadingAllStores}"
        >
            Выберите существующий магазин
        </span>

        <div v-else class="select__inner">
            <span>{{ selectedStore.id }}</span>
            <span>{{ selectedStore.name }}</span>
            <div class="select__item-img" v-if="selectedStore.image">
                <img :src="selectedStore.image" alt="store_img">
            </div>
        </div>

        <div class="select__options" v-if="isShowOptions">
            <div class="select__item" @click="setSelect(null)">Выберите существующий магазин</div>
            <lavel
                class="select__item"
                v-for="store in allStores"
                :key="store.id"
                @click="setSelect(store)"
            >
                <input type="checkbox" :checked="selectedStore && store.id === selectedStore.id">
                <span>{{ store.id }}</span>
                <span>{{ store.name }}</span>
                <div class="select__item-img" v-if="store.image">
                    <img :src="store.image" alt="store_img">
                </div>
            </lavel>
        </div>
    </div>
</template>

<script>
import {mapActions, mapState} from "vuex";

export default {
    name: "select-store",
    computed: {
        ...mapState('store', ['allStores', 'isLoadingAllStores'])
    },
    data() {
        return {
            isShowOptions: false
        }
    },
    props: {
        selectedStore: {
            default: null
        }
    },
    created() {
        this.loadAllStores()
    },
    methods: {
        ...mapActions('store', ['loadAllStores']),
        setSelect(value) {
            this.$emit('update:selectedStore', value)
        },
        toggleShowOptions() {
            this.isShowOptions = !this.isShowOptions
        },
    }
}
</script>

<style scoped lang="scss">
    .select {
        min-width: 170px;
        display: flex;
        -moz-box-pack: justify;
        justify-content: space-between;
        -moz-box-align: center;
        align-items: center;
        position: relative;
        padding: 8px;
        cursor: pointer;
        user-select: none;
        border-radius: 4px;
        border: 1px solid #d5dae0;

        &__inner {
            display: flex;
            -moz-box-align: center;
            align-items: center;
            width: 100%;
            &-disabled {
                color: grey;
            }

            & > :not(:last-child) {
                margin-right: 10px;
            }
        }

        &__options {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            transform: translateY(4px);
            border-radius: 4px;
            z-index: 15;
            box-shadow: rgba(0, 0, 0, 0.08) 0px 4px 16px;
            overflow-x: hidden;
            overflow-y: auto;
            height: 243px;
            background: #fff;
            border: 1px solid #dadada;
            //box-shadow: 0 2px 8px rgba(0,0,0,.16);
        }

        &__item {
            display: flex;
            align-items: center;
            padding: 8px 16px;
            transition: background-color 0.3s ease 0s;
            height: 40px;

            &:not(:last-child) {
                border-bottom: 1px solid rgba(0, 0, 0, 0.12);
            }

            & > :not(:last-child) {
                margin-right: 10px;
            }

            &:hover {
                background-color: rgb(192, 201, 240);

            }

            &-img {
                height: 30px;
                display: flex;
                justify-content: center;
                align-items: center;

                & img {
                    max-height: 100%;
                    max-width: 100%;
                }
            }
        }
    }
</style>