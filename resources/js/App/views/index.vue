<template>
    <div class="main">
        <div class="main__row" v-if="isLoadingCategories">
            <div
                    class="main__row-item skeleton-loader"
                    v-for="item in [1,2,3,4,5,6,7,8]"
                    :key="item"
            ></div>
        </div>
        <div class="main__row" v-else>
            <router-link
                    class="main__row-item"
                    v-for="category in categories"
                    :key="category.id"
                    :to="{name:'category', params: {'category_code': category.code}}"
            >
                <img v-lazyload :data-src="category.image" alt="category.image">
                <div class="subtitle">{{ category.name }}</div>
            </router-link>
        </div>
        <div class="main__row">
            <journal></journal>
        </div>
    </div>
</template>

<script setup>
    import journal from "../components/journal";
    import { onMounted } from 'vue';
    import {useCategoryStore} from "../store/category";
    import { storeToRefs } from "pinia";

    const categoryStore = useCategoryStore();
    const { isLoadingCategories, categories } = storeToRefs(categoryStore);
    onMounted(() => {
        if (!categories.length) {
            categoryStore.loadNestedCategories()
        }
    })
</script>

<style lang="scss" scoped>
    @keyframes load {
        from {
            left: -150px;
        } to {
              left: 100%;
          }
    }

    .main {
        margin-top: 30px;
        &__style {
            box-shadow: 0 8px 8px 0 rgba(0,0,0,0.05),0 29px 26px 0 rgba(0,0,0,0.08);
            border-radius: 8px;
            background: #fff;
            width: 100%;
            padding: 30px;
            &.about {
                margin-bottom: 20px;
            }
        }
        &__row {
            display: flex;
            justify-content: space-between;
            margin-bottom:15px;
            flex-wrap: wrap;
            &-item {
                text-decoration: none;
                width: calc(100% /4 - 10px);
                align-items: center;
                justify-content: space-between;
                cursor: pointer;
                position: relative;
                user-select: none;
                border-radius: 8px;
                box-shadow: 0 1px 2px 0 rgba(0,0,0,0.16);
                display: inline-flex;
                flex-direction: column;
                vertical-align: top;
                margin: 0 0 12px 0;
                background: #fff;
                padding: 30px;
                height: 280px;
                &:hover {
                    box-shadow: 0 8px 8px 0 rgba(0,0,0,0.05),0 29px 26px 0 rgba(0,0,0,0.08);
                }
                & img {
                    display: inline-block;
                    text-align: center;
                    width: 100%;
                    height: 70%;
                    line-height: 150px;
                    object-fit: cover;
                }
                & .subtitle {
                    display: inline-block;
                    margin-bottom: 10px;
                    text-align: center;
                    width: 100%;
                    font-size: 16px;
                    font-weight: bold;
                    color: #4e4e4e;
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
        }
    }

    @media (max-width: 1000px) {
        .main__row-item {
            height: 240px;
        }
    }

    @media (max-width: 700px) {
        .main__row-item {
            width: calc(100% /2 - 10px);
            height: 200px;
            padding:20px
        }
    }
    @media (max-width: 400px) {
        .main__row-item {
            width: calc(100% /2 - 5px);
            height: 150px;
            padding: 10px;
        }
    }
</style>
