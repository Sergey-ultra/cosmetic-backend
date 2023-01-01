<template>
    <div ref="filter">
        <div  :style="filterStyle" ref="filterBlock">
           <slot></slot>
        </div>
    </div>
</template>

<script setup>
    import {computed, watch, ref} from "vue";
    import {useFilterStore} from "../../store/filter";
    import {storeToRefs} from "pinia";

    const filterState = ref('relative');
    const translateY = ref(0);
    const filter = ref(null);
    const filterBlock = ref(null);

    const filterStore = useFilterStore();
    const { productDom } = storeToRefs(filterStore);


    const filterStyle = computed(() => {
        if (filterState.value === 'relative') {
            return {
                position: 'relative',
                transform: `translate(0,0)`,
            }
        }
        if (filterState.value === 'relativeCalculated') {
            return {
                position: 'relative',
                transform: `translate(0,${translateY.value}px)`,
            }
        }
        if (filterState.value === 'bottomFixed') {
            return {
                position: 'fixed',
                top: 'auto',
                bottom: '10px',
                width: filter.value.offsetWidth + 'px'
            }
        }
        if (filterState.value === 'topFixed') {
            return {
                position: 'fixed',
                top: '10px',
                width: filter.value.offsetWidth + 'px'
            }
        }
    });

    const getCoordsY = elem =>  {
        let box = elem.getBoundingClientRect();
        //возвращает координаты ерхнего края элемента относительно viewport + отступ от края window
        //+ pageXOffset
        return {top: box.top, bottom: box.bottom };
    };

    watch(productDom, (value, oldValue) => {
        const screenBottomY = document.documentElement.clientHeight - 10
        const screenTopY = 10
        let isMoveDown = value.bottom < oldValue.bottom


        const filterBlockTop = this.getCoordsY(filterBlock.value).top
        const filterBlockBottom = this.getCoordsY(filterBlock.value).bottom


        const filterBottomCondition = filterBlockBottom <= screenBottomY
        const screenBottomCondition = value.bottom >= screenBottomY


        if (['relative', 'relativeCalculated'].includes(filterState.value) && filterBottomCondition) {
            filterState.value = 'bottomFixed';
        }
        if (filterState.value === 'bottomFixed' && (!screenBottomCondition || (screenBottomCondition && !isMoveDown))) {
            filterState.value = 'relativeCalculated';
        }


        if (filterState.value === 'relativeCalculated' && !isMoveDown && filterBlockTop >= screenTopY) {
            filterState.value = 'topFixed';
        }

        const filterTopCondition = filterBlockTop <= value.top
        if (filterState.value === 'topFixed' && !isMoveDown && filterTopCondition) {
            filterState.value = 'relative';
        }

        if (filterState.value === 'topFixed' && isMoveDown) {
            filterState.value = 'relativeCalculated';
        }
    });

    watch(
        filterState,
        (value, oldValue) => {
            const screenBottomY = document.documentElement.clientHeight - 10;
            const fromProductTopToScreenBottomHeight = screenBottomY - productDom.value.top;
            const filterHeight = filterBlock.value.offsetHeight;

            const maxTranslateY = productDom.value.height - filterHeight;
            const translate = fromProductTopToScreenBottomHeight - filterHeight;

            if (value === 'relativeCalculated' && oldValue === 'bottomFixed' && translate > 0) {
                translateY.value = translate >= maxTranslateY ? maxTranslateY : translate;
            }

            const screenTopY = 10;
            if (value === 'relativeCalculated' && oldValue === 'topFixed') {
                translateY.value = screenTopY - productDom.value.top;
            }
        }
    );
</script>

<style scoped>

</style>