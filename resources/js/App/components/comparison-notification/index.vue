<template>
    <div class="current-compare" v-if="currentComparedSku !== null">
        <div class="container">
            <div class="current-compare__wrapper">
                <div class="current-compare__img">
                    <img  :src="currentComparedSku.image" :alt="currentComparedSku.image">
                </div>
                <div class="current-compare__inner">
                    <div class="current-compare__title">
                        Товар {{ currentComparedSku.name }} {{ currentComparedSku.volume }}
                        <span v-if="currentComparedSku.status === 'add'">добавлен в сравнение</span>
                        <span v-if="currentComparedSku.status === 'remove'">удален из сравнения</span>
                    </div>
                    <div>Всего в списке {{ allComparedSkuIdsCount }} товаров из
                        <span v-if="compared.length === 1">категории {{ compared[0].name }}</span>
                        <span v-if="compared.length > 1">{{ compared.length }} категорий</span>
                    </div>
                </div>
                <div
                        v-if="currentComparedSku.status === 'add'"
                        class="current-compare__link"
                        @click="comparisonStore.setCurrentComparedSkuToNull"
                >
                    <router-link class="current-compare__link-btn" :to="{ name: 'comparison' }">Сравнить</router-link>
                </div>
                <div class="current-compare__button"  @click="comparisonStore.setCurrentComparedSkuToNull">
                    <span class="current-compare__icon">
                        ✕
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import {useComparisonStore} from "../../store/comparison";
    import {storeToRefs} from "pinia";

    const comparisonStore = useComparisonStore();
    const { currentComparedSku, compared, allComparedSkuIdsCount } = storeToRefs(comparisonStore);
</script>

<style lang="scss" scoped>
    @keyframes dropDown {
        from {
            transform: translate3d(0,-100%,0);
        }
        to {
            transform: translateZ(0);
        }
    }
    .current-compare {
        position: fixed;
        z-index: 35000;
        top: 0;
        width: 100%;
        animation: dropDown .6s cubic-bezier(.7,0,.3,1);
        opacity: 1;
        background: #fff;
        padding: 5px 0;
        &:before {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            content: "";
            box-shadow: 0 10px 20px rgba(0,0,0,.1);
        }
        &__wrapper {
            position: relative;
            margin: 0 auto;
            display: flex;
            flex-wrap:wrap;
            align-items: center;
            width: 100%;
        }
        &__img {
            width: 60px;
            height: 60px;
            white-space: nowrap;
            background: #fff;
            & img {
                max-width: 100%;
                max-height: 100%;
            }
        }
        &__inner {
            flex: 1 1 auto;
            padding: 9px 0 9px 20px;
            margin-right: auto;
            width: 570px;
            font-size: 14px;
            line-height: 20px;
            vertical-align: middle;
            word-wrap: break-word;
            word-break: break-word;
            color: #666;
        }
        &__title {
            line-height: 20px;
            word-wrap: break-word;
            word-break: break-word;
            color: #666;
            margin-bottom: 6px;
            font-size: 18px;
        }
        &__link {
            margin-right: 32px;
            &-btn {
                font-size: 14px;
                height: 32px;
                line-height: 32px;
                background-image: linear-gradient(to bottom, #ffd6e7, #f88cb6);
                border-radius: 4px;
                padding: 0 16px;
                display: inline-block;
                text-align: center;
                color: #222;
                cursor: pointer;
                transition: color .12s ease-out;
                text-decoration: none;
                outline: 0;
                touch-action: manipulation;
            }
        }
        &__button {
            letter-spacing: 0;
            background-color: transparent;
            user-select: none;
            cursor:pointer;
            height: 40px;
            display:flex;
            justify-content: center;
            align-items: center;
            padding: 0 16px;
            border: none;
        }
        &__icon {
            width: 16px;
            height: 16px;
            min-width: 16px;
            font-weight: 500;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #878787;
            font-size: 16px;
            line-height: 38px;
            & svg {
                max-height: 14px;
                max-width: 14px;
            }
        }
    }
</style>