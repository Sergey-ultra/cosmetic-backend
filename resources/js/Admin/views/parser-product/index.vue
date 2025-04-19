<template>
    <tabs :tabList="tabList">
        <template v-slot:tabPanel-1>
            <productParser/>
        </template>
        <template v-slot:tabPanel-2>
            <compressImage/>
        </template>
    </tabs>
</template>

<script setup>
import Tabs from "../../components/tabsComponent.vue";
import productParser from "./src/product-parser.vue";
import compressImage from "./src/compress-images.vue"

const tabList = ['Парсер товаров', 'Сжатие картинок'];

</script>

