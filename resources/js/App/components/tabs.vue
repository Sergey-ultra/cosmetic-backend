<template>
    <div class="tab">

        <div
            v-for="(tab,index) in tabs" :key="index"
            :class="{'active':tab.to.name ===  $route.name }"
            class="tab__item"
        >
            <router-link :to="tab.to" class="">
                {{ tab.name }}
                <sub v-if="tab.sub">{{ tab.sub }}</sub>
            </router-link>
        </div>

    </div>
</template>

<script setup>
    import {defineProps} from "vue";

    const props = defineProps({
        tabs: Array
    });
</script>

<style lang="scss" scoped>
    .tab {
        width: 100%;
        display:flex;
        border-radius:11px;
        overflow-x:auto;
        overflow-y:hidden;
        &__item {
            /*padding: 7px 20px;*/
            font-weight: 700;
            display:flex;
            justify-content: center;
            padding: 6px 0;
            margin-right: 25px;
            font-size: 14px;
            line-height: 18px;
            white-space: nowrap;
            &  a {
                text-decoration: none;
                color: #04b;
                &:hover {
                    color:#46bd87;
                }
            }
            &.active {
                & a {
                    font-size: 16px;
                    color: #202020;
                    cursor: default;
                }
            }
        }
    }
    @media (max-width: 800px) {

    }

</style>
