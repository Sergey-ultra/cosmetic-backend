<template>
    <ul class="pagination">
        <li class="pagination__item">
            <fa icon="angle-double-left" @click="setFirstPage"></fa>
        </li>
        <li class="pagination__item">
            <fa icon="angle-left" @click="minusPage"></fa>
        </li>
        <li
                :class="{'page__active': p === page}"
                class="pagination__item"
                v-for="p in paginationList"
                :key="p"
                @click="page =  p"
        >
            {{ p }}
        </li>
        <li class="pagination__item">
            <fa icon="angle-right" @click="plusPage"></fa>
        </li>
        <li class="pagination__item">
            <fa icon="angle-double-right" @click="setLastPage"></fa>
        </li>
    </ul>
</template>

<script setup>
    import {defineProps, defineEmits, computed} from "vue";

    const emit = defineEmits(['update:currentPage']);
    const props = defineProps({
        lastPage: Number,
        currentPage: Number,
        paginationPortionSize: {
            type: Number,
            default: 9
        }
    });

    let page = computed({
        get() {
            return props.currentPage;
        },
        set(value) {
            emit('update:currentPage', value);
        }
    });

    const size = computed(() => (props.paginationPortionSize - 1) / 2);

    const borders = computed(() => {
        if (page.value < size.value) {
            return {left: 1, right: props.paginationPortionSize};
        } else if (page.value > props.lastPage - size.value) {
            return {
                left: props.lastPage - props.paginationPortionSize + 1,
                right: props.lastPage
            };
        }

        return {
            left: page.value - size.value,
            right: page.value + size.value
        };
    });

    const portionCount = computed(() => Math.ceil(props.lastPage / props.paginationPortionSize));

    const paginationList = computed(() => {
        let pages = [];
        for (let i = 1; i <= props.lastPage; i++) {
            pages.push(i);
        }
        return pages.filter(page => (page >= borders.value.left && page <= borders.value.right));
    });


    const setFirstPage = () => {
        if (page.value !== 1) {
            page.value = 1;
        }
    };

    const setLastPage = () => {
        if (page.value !== props.lastPage) {
            page.value = props.lastPage;
        }
    };

    const minusPage = () => {
        if (page.value > 1) {
            page.value--;
        }
    };

    const plusPage = () => {
        if (page.value < props.lastPage) {
            page.value++;
        }
    };
</script>

<style lang="scss" scoped>
    .pagination {
        margin-top: 20px;
        display: flex;
        justify-content: center;
        border-radius: 8px;
        background-color: #fff;
        box-sizing: unset;
        box-shadow: 0 1px 2px 1px rgba(0, 0, 0, 0.2);
        border: 0;
        height: 48px;
        font-size: 16px;
        list-style: none;
        padding: 0;
        text-align: center;
        max-width: 100vw;
        margin-bottom: 0;
        &__item {
            display: flex;
            align-items:center;
            justify-content: center;
            border: solid 3px transparent;
            color: #8c8c8c;
            font-weight: bold;
            font-size: 16px;
            box-sizing: border-box;
            width: 65px;
            height: 49px;
            cursor:pointer;
        }
    }
    .page__active {
        border: #fff;
        border-bottom: solid 3px #fc8507;
    }
</style>