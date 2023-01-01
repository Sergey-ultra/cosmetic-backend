<template>
    <h2 class="title">{{ $route.meta.title }}</h2>

    <div class="favorites">
<!--        <loader v-if="isLoadingFavoriteSkus"/>-->
        <div class="favorites__wrapper">
            <div
                    class="favorites__item"
                    v-for="sku in favoriteSkus"
                    :key="sku.id"
            >
                <router-link
                    class="favorites__img"
                    :to="{ name: 'description', params: { product_code: sku.code + '-' + sku.id }}"
                >
                    <img :src="sku.images" :alt="sku.images">
                </router-link>

                <div class="favorites__main">
                    <router-link
                            :to="{ name: 'description', params: { product_code: sku.code + '-' + sku.id }}"
                    >
                        <div>{{sku.name}} {{ sku.volume }}</div>
                    </router-link>
                    <div class="favorites__layer">
                        <div
                                class="favorites__delete"
                                @click=" favoritesStore.removeFromFavorites(sku.id)"
                        >
                            <fa class="icon" icon="trash"></fa>
                        </div>
                    </div>

<!--                    <chart :chartData="sku.priceDynamics"/>-->

                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import chart from '../../components/sku-price-history-chart'
    import loader from '../../components/loader'
    import { storeToRefs } from "pinia";
    import {useFavoritesStore} from "../../store/favorites";
    import {useAuthStore} from "../../store/auth";
    import { onMounted } from 'vue';

    const favoritesStore = useFavoritesStore();
    const authStore = useAuthStore();
    const { favoriteSkus, isLoadingFavoriteSkus } = storeToRefs(favoritesStore);
    const { isAuth } = storeToRefs(authStore);

    onMounted(() => {
        if (isAuth.value) {
            favoritesStore.loadFavoriteSkus();
        }
    });
</script>

<style lang="scss" scoped>
    .favorites {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.16);
        width: 100%;
        margin-bottom: 15px;
        &__wrapper {

        }
        &__item {
            padding: 20px;
            position: relative;
            display: flex;
            height: 200px;
            background-color: transparent;
            &:not(:last-child) {
                border-top: 1px solid rgba(0, 0, 0, 0.1);
            }
            &:first-child {
                border-top-left-radius: 8px;
                border-top-right-radius: 8px;
            }
            &:last-child {
                border-bottom-left-radius: 8px;
                border-bottom-right-radius: 8px;
            }
        }
        &__img {
            height: 150px;
            width: 150px;
            flex-shrink: 0;
            display:flex;
            justify-content: center;
            align-items:center;
            margin-right: 20px;
            & img {
                max-height: 100%;
                max-width: 100%;
            }
        }
        &__main {
            display: flex;
            flex-direction: column;
        }
        &__delete {
            cursor:pointer;
        }
    }

</style>