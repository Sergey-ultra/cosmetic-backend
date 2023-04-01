<template>
    <div class="tab">
        <div
            v-for="(tab,index) in tabs"
            :key="index"
            :class="{'active':tab === currentTab }"
            class="tab__item"
        >
           <span @click="setComponent(tab)">
               {{ tab.name }}
           </span>
        </div>
    </div>

</template>

<script setup>
    import {ref} from "vue";

    let currentTab = ref('priceParser')

    const props = defineProps({
        tabs: Array
    });

    const setCurrentTab = value => currentTab.value = value;
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
