<template>
    <tabs :tabList="tabList">
        <template v-slot:tabPanel-1>
            <priceParser/>
        </template>
        <template v-slot:tabPanel-2>
            <manualPriceParser/>
        </template>
    </tabs>
</template>

<script setup>
   import tabs from "../../components/tabsComponent.vue";
   import priceParser from "./src/price-parser.vue";
   import manualPriceParser from "./src/manual-price-parser.vue"

   const tabList = ['Парсер цен', 'Ручной парсер цен'];
</script>

<style scoped>

</style>
