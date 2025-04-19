<template>
    <tabs :tabList="['Список юзеров', 'Создание ботов']">
        <template v-slot:tabPanel-1>
            <list/>
        </template>
        <template v-slot:tabPanel-2>
            <bots/>
        </template>
    </tabs>
</template>
<script setup>
import list from "./list.vue";
import bots from "./bots.vue";
import tabs from "../../components/tabsComponent.vue";

</script>
<style scoped lang="scss">

</style>
