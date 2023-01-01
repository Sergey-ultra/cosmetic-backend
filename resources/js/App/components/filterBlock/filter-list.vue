<template>
    <div v-if="filterElements && filterElements.length" class="collapse">
        <div class="collapse__link" @click="collapse">
            <fa class="collapse__icon" icon="angle-down" :class="{'collapse__icon-rotate': isCollapse}"></fa>
            <span>{{ title }}</span>
        </div>
        <div class="collapse__content"  v-if="!isCollapse">

            <div class="input__search">
                <div class="input__search-icon">
                    <fa icon="search"></fa>
                </div>
                <input type="text" class="input__search-input" placeholder="Поиск" v-model.trim="search">
            </div>

            <label
                    class="checkbox"
                    :class="{'checkbox__shadow': isShowAllFilterElements, 'checkbox__border': !isShowAllFilterElements}"
                    @change="$emit('showFilterSubmit', $event)"
            >
                <input type="checkbox" class="checkbox__input" v-model="isSelectAllLocal">
                <span class="checkbox__check"></span>
                <span>Все {{ title.toLowerCase() }}</span>
            </label>

            <div class="checkbox__list">
                <label
                        class="checkbox"
                        v-for="(element, index) in elements"
                        :key="index"
                        @change="$emit('showFilterSubmit', $event)"
                >
                    <input type="checkbox" class="checkbox__input" :value="element[elementKey]" v-model="filterParams">
                    <span class="checkbox__check"></span>
                    <span>
                        {{ element.name }}
                        <span class="filter__count">({{ element.count }})</span>
                    </span>
                </label>
            </div>

            <div>
                <span
                        v-if="filterElements.length > 7"
                        class="collapse__content-element show-all"
                        @click="toggleIsShowAllFilterElements"
                >
                    {{ isShowAllFilterElements ? 'Свернуть все' : 'Показать все' }}
                </span>
                <span
                        class="reset-filter"
                        v-if="filterParams.length"
                        @click="resetLocalFilter($event)"
                >
                    Сбросить
                </span>
            </div>
        </div>
    </div>
</template>

<script setup>
    import {computed, onMounted, watch, ref, defineProps, defineEmits} from "vue";
    import {storeToRefs} from "pinia";
    import {useFilterStore} from "../../store/filter";

    const filterStore = useFilterStore();
    const { filters } = storeToRefs(filterStore);

    const isCollapse = ref(false);
    const isShowAllFilterElements = ref(false);
    const search = ref('');
    const isSelectAll = ref(false);

    const props = defineProps({
        filterKey: String,
        localFilterParams: Array,
    });

    const emit = defineEmits(['update:localFilterParams', 'update:localFilterParams', 'showFilterSubmit']);


    const title = computed(() => {
        switch (props.filterKey) {
            case 'categories':
                return 'Категории';
            case 'brands':
                return 'Бренды';
            case 'volumes':
                return 'Объемы';
            case 'purposes':
                return 'Цели';
            case 'ingredients':
                return 'Активные ингредиенты';
            case 'extracts':
                return 'Масла и экстракты';
            case 'countries':
                return 'Страны';
            default:
                return '';
        }
    });

    const elementKey = computed(() => {
        if (['volumes', 'purposes'].includes(props.filterKey)) {
            return 'name';
        }
        return 'id';
    });

    const filterElements = computed(() => filters.value[props.filterKey]);

    const filterParams = computed({
        get() {
            return props.localFilterParams;
        },
        set(value) {
            emit('update:localFilterParams', value);
        }
    });


    const isSelectAllLocal = computed({
        get() {
            return isSelectAll.value;
        },
        set(value) {
            isSelectAll.value = value;
            filterParams.value = value ? [...filterElements.value.map(el => el[elementKey.value])] : [];
        }
    });

    const searchingFilterElements = computed(() => {
        if (filterElements.value) {
            if (search.value === '') {
                return  filterElements.value;
            }
            return filterElements.value.filter(el => el.name.toLowerCase().includes(search.value.toLowerCase()));
        }
        return [];
    });

    const elements = computed(() => isShowAllFilterElements.value ? searchingFilterElements.value : searchingFilterElements.value.slice(0,7));

    const collapse = () => isCollapse.value = ! isCollapse.value;

    const toggleIsShowAllFilterElements = () => isShowAllFilterElements.value = ! isShowAllFilterElements.value;

    const resetLocalFilter = event => {
        emit('update:localFilterParams', []);
        emit('showFilterSubmit', event);
        isSelectAll.value = false;
    };

    watch(props.localFilterParams, value => {
        if (value.length === 0) {
            search.value = '';
            isSelectAll.value = false;
        }
    });

    watch(search, value => {
        if (value !== '') {
            isShowAllFilterElements.value = true;
        }
    });
</script>

<style lang="scss" scoped>
    .collapse {
        width: 100%;
        &__link {
            display: flex;
            align-items:center;
            padding: 8px 24px 8px 13px;
            font-weight: bold;
            &:hover {
                background-color: #f0ffd6;
            }
        }
        &__content {
            padding-bottom: 10px;
            &-item {
                margin: 5px 15px 8px;
            }
            &-element {
                margin-left: 16px;
            }
        }
        &__icon {
            color: #afafaf;
            display: inline-flex;
            font-size: 20px;
            height: 20px;
            width: 20px;
            align-items:center;
            justify-content: center;
            margin: 5px 8px 5px 0;
            padding-left: 0;
            vertical-align: top;
            transform: rotate(0deg);
            transition: all .3s ease-in ;
            &-rotate {
                transform: rotate(180deg);
            }
        }
    }
    .input__search {
        margin: 5px 15px 8px;
        position: relative;
        border-radius: 8px;
        height: 40px;
        line-height: 40px;
        font-size: 14px;
        transition: background .3s;
        &-input {
            color: #333;
            background: #fff;
            padding: 0 50px 0 12px;
            height: 34px;
            width: 100%;
            border-radius: 8px;
            border: none;
            outline: none;
            transition: background .3s;
        }
        &-icon {
            color: #afafaf;
            position: absolute;
            right: 5px;
            top: 0;
            font-size: 16px;
            height: 100%;
            width: 40px;
            text-align: center;
            cursor: pointer;
        }
    }

    .checkbox {
        display: block;
        position: relative;
        font-size: 14px;
        line-height: 24px;
        user-select: none;
        cursor: pointer;
        color: #333;
        padding: 6px 12px 6px 38px;
        transition: 0.3s;
        margin: 0;
        &:hover {
            background-color: #fff7da;
        }

        &__border {
            border-bottom: 1px solid #e5e5e5;
        }
        &__shadow {
            box-shadow: 0 5px 4px -1px rgba(0,0,0,.15);
        }
        &__list {
            max-height: 252px;
            overflow-x: hidden;
            overflow-y: auto;
        }

        &__input{
            position: absolute;
            opacity: 0;
            top: 50%;
            height: 0;
            margin: 0 !important;
            &:checked + .checkbox__check{
                background-color: #42b983;
                border: none;
                &::after {
                    content: "";
                    display: block;
                    width: 7px;
                    height: 4px;
                    border-bottom: 2px solid #fff;
                    border-left: 2px solid #fff;
                    position: absolute;
                    top: 45%;
                    left: 51%;
                    transform: translate(-50%, -50%) rotate(-45deg);
                }
            }
        }
        &__check {
            content: "";
            display: block;
            width: 14px;
            height: 14px;
            border: 1px solid #ccc;
            border-radius: 2px;
            background-color: #fff;
            position: absolute;
            top: 50%;
            transform: translate(0, -50%);
            left: 12px;
        }
    }


    .search-icon-box {
        position: absolute;
        right: 5px;
        top: 0;
        height:100%;
        width: 40px;
        max-height: 40px;
        text-align: center;
        cursor: pointer;
    }
    .search-icon {
        color: #afafaf;
    }
    .filter__count {
        color: #a0a2a5;
    }
    .show-all {
        border-bottom: 1px dotted;
        color: #2c509a;
        padding-top: 5px;
        cursor: pointer;
        font-size: 13px;
    }
    .reset-filter {
        cursor: pointer;
        margin-right: 16px;
        padding-top: 5px;
        font-size: 13px;
        float: right;
        border-bottom: 1px dotted;
    }
</style>