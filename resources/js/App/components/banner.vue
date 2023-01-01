<template>
    <div v-if="isShowBanner && !isMobileScreenWidth" class="banner">
        <div v-if="isLoadingBanner" class="banner__skeleton skeleton-loader">

        </div>
        <div  v-else class="banner__container">
            <tool-tip class="banner__close">
                <template v-slot:content>
                    Скрыть баннер
                </template>
                <div  @click="bannerStore.setShowBanner(false)">✕</div>
            </tool-tip>
            <div class="banner__wrap">
                <a class="banner__link" href="http://fantasticskin.ru">
                    <img :src="src" :alt="bannerAlt">
                </a>
            </div>
        </div>
    </div>

</template>

<script setup>
    import toolTip from './tool-tip'
    import { storeToRefs } from "pinia";
    import {useBannerStore} from "../store/banner";
    import {useProductStore} from "../store/product";
    import {computed, onBeforeMount, ref, watch} from "vue";

    const bannerStore = useBannerStore();
    const productStore = useProductStore();
    const { isLoadingBanner, bannerUrls, isShowBanner } = storeToRefs(bannerStore);
    const { productsWithPagination } = storeToRefs(productStore);

    let src = ref('');



    const isMobileScreenWidth = computed(() => document.documentElement.clientWidth <= 700);

    const bannerAlt = computed(() => {
        const array = src.value.split('/');
        return array[array.length - 1];
    });

    const getRandom = () => {
        if (bannerUrls.value.length) {
            let rand = Math.random() * (bannerUrls.value.length);
            let index = Math.floor(rand);
            src.value = bannerUrls[index];
        }
    };

    watch(productsWithPagination, () => getRandom());

    onBeforeMount(() => getRandom());
</script>

<style lang="scss" scoped>
    @keyframes load {
        from {
            left: -150px;
        }
        to {
            left: 100%;
        }
    }

    .skeleton-loader {
        position:relative;
        overflow: hidden;
        &:before {
            content: '';
            display: block;
            position: absolute;
            left: -150px;
            top: 0;
            height: 100%;
            width: 150px;
            background: linear-gradient(to right, transparent 0%, #E8E8E8 50%, transparent 100%);
            animation: load 1s cubic-bezier(0.4, 0.0, 0.2, 1);
        }
    }
    .banner {
        width: 100%;
        margin-bottom: 10px;
        &__skeleton {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 2px 0 rgba(0,0,0,0.16);
            height:80px;
        }
        &__container {
            position: relative;
        }

        &__close {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            color: #fff;
            text-align: center;
            text-decoration: none;
            background-color: rgba(0,0,0,0.3);
            font-size: 13px;
            line-height: 24px;
            position: absolute !important;
            top: 10px;
            right: 10px;
            cursor: pointer;
            z-index: 10;
            &:hover {
                background: #000;
            }
        }
        &__wrap {

        }

        &__link img {
            border-radius: 8px;
            width: 100%;
        }
    }
</style>