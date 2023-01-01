<template>
    <div v-if="filterElements && filterElements.length" class="collapse">
        <div class="collapse__link" @click="show">
            <span class="collapse__component">{{ title }}</span>
            <div class="collapse__show">
                <span
                        class="reset-filter"
                        v-if="filterParams.length"
                        @click="resetLocalFilter($event)"
                >
                    Сбросить
                </span>
                <fa class="collapse__icon" icon="angle-down"></fa>
            </div>
        </div>
        <div class="collapse__selected">
            <div
                    class="collapse__selected-item"
                    v-for="filter in filterParams"
                    :key="filter"
            >
                {{ getFilterName(filter) }}
                <div class="collapse__selected-item-close" @click="deleteParam(filter)">
                    <div>✕</div>
                </div>
            </div>
        </div>

        <div class="collapse__content"  v-if="!isCollapse">

            <div class="collapse__header">
                <div class="collapse__title">
                    <div class="collapse__hide" @click="hide">
                        <fa icon="arrow-left"></fa>
                    </div>
                    <span class="collapse__name">{{ title }}</span>
                    <button class="collapse__close" @click="$emit('hideMobileFilter')">
                        <span>✕</span>
                    </button>
                </div>

                <div class="input__search">
                    <div class="input__search-icon">
                        <fa icon="search"></fa>
                    </div>
                    <input type="text" class="input__search-input" placeholder="Поиск" v-model.trim="search">
                </div>

                <label class="checkbox checkbox__shadow">
                    <input type="checkbox" class="checkbox__input" v-model="isSelectAllLocal">
                    <span class="checkbox__check"></span>
                    <span>Все {{ title.toLowerCase() }}</span>
                </label>
            </div>


            <div class="checkbox__list">
                <label
                        class="checkbox"
                        v-for="(element, index) in searchingFilterElements"
                        :key="index"
                >
                    <input type="checkbox" class="checkbox__input" :value="element[elementKey]" v-model="filterParams">
                    <span class="checkbox__check"></span>
                    <span>
                        {{ element.name }}
                        <span>({{ element.count }})</span>
                    </span>
                </label>
            </div>

            <div class="apply__wrapper" v-if="filterParams.length">
                <div class="apply__btn" @click="filterSubmit">Применить</div>
            </div>
        </div>
    </div>

</template>

<script setup>
    import {useFilterStore} from "../../store/filter";
    import {defineProps, defineEmits, ref, computed} from "vue";
    import {storeToRefs} from "pinia";

    const emit = defineEmits(['update:localFilterParams']);
    const props = defineProps({
        filterKey: String,
        localFilterParams: Array,
    });

    const filterStore = useFilterStore();
    const { filters } = storeToRefs(filterStore);

    let isCollapse = ref(true);
    let search = ref('');
    let isSelectAll = ref(false);


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

    const filterElements = computed(() =>  filters.value[props.filterKey]);

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
                return filterElements.value;
            }
            return this.filterElements.value.filter(el => el.name.toLowerCase().includes(search.value.toLowerCase()));
        }
        return [];
    });


    const getFilterName = value => {
        if (elementKey.value === 'name') {
            return value;
        }
        return filterElements.value.find(el => el.id === Number(value)).name;
    };

    const show = () => {
        isCollapse.value = false;
    };

    const hide = () => {
        isCollapse.value = true;
        emit('update:localFilterParams', []);
        isSelectAll.value = false;
    };

    const filterSubmit = () => {
        isCollapse.value = true;
        isSelectAll.value = false;
    };

    const deleteParam = value => filterParams.value = filterParams.value.filter(el => el !== value);

    const resetLocalFilter = event => {
        event.stopPropagation();
        emit('update:localFilterParams', []);
        isSelectAll.value = false;
    };
</script>

<style lang="scss" scoped>
    .collapse {
        padding: 15px 0;
        border-top: 1px solid #ebeff4;
        &__link {
            padding:0 15px ;
            display: flex;
            align-items:center;
            justify-content: space-between;
        }
        &__component {
            font-weight: bold;
        }
        &__show {
            display: flex;
            align-items:  center;
        }

        &__selected {
            margin-top: 10px;
            padding: 0 15px;
            display: flex;
            align-items: center;
            overflow-y: hidden;
            overflow-x: auto;

            &-item {
                white-space: nowrap;
                font-size: 13px;
                height: 30px;
                line-height: 24px;
                padding: 5px 8px 5px 12px;
                position: relative;
                margin: 0 10px 10px 0;
                display: flex;
                align-items: center;
                cursor: pointer;
                background: #ffb1ca;
                border-radius: 100px;
                color: #fff;
                border: 1px solid #f88cb6;
                &-close {
                    padding: 0 5px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 20px;
                    height: 20px;
                    border-radius: 50%;
                    color: #fff;
                    text-align: center;
                    text-decoration: none;
                    background-color: rgba(0, 0, 0, 0.14);
                    font-size: 13px;
                    line-height: 24px;
                    margin-left: 8px;
                    cursor: pointer;

                    &:hover {
                        background: #000;
                    }
                }

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
        }
        &__content {
            background-color: #f8f8f8;
            display: block;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 202;
            width: 100%;
            height: 100%;
            overflow: hidden;
            outline: 0;
            &-item {
                margin: 5px 15px 8px;
            }
        }
        &__title {
            position: relative;
            padding: 0 15px;
            height:40px;
            display:flex;
            align-items: center;
        }
        &__hide {
            opacity: 0.6;
            margin-right:10px;
        }
        &__name {
            font-weight: bold;
        }
        &__close {
            position: absolute;
            right: 15px;
            cursor: pointer;
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
            color: #000;
            text-shadow: 0 1px 0 #fff;
            opacity: .5;
            padding: 0;
            border-style: none;
            background-color: transparent;
        }
    }

    .input__search {
        margin: 5px 15px 8px;
        position: relative;
        border-radius: 8px;
        height: 36px;
        line-height: 36px;
        font-size: 14px;
        transition: background .3s;
        border: 1px solid #999999;
        background-color: transparent;
        &-input {
            color: #333;
            background-color: transparent;
            padding: 0 50px 0 12px;
            height: 100%;
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
        padding: 8px 12px 8px 38px;
        transition: 0.3s;
        margin: 0;

        &__border {
            border-bottom: 1px solid #e5e5e5;
        }
        &__shadow {
            box-shadow: 0 5px 4px -1px rgba(0,0,0,.15);
        }
        &__list {
            max-height: calc(100% - 129px);
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


    .apply {
        &__wrapper {
            background-color: #fff;
            position: absolute;
            bottom: 0;
            width:100%;
            height: 60px;
            display:flex;
            align-items: center;
            justify-content: center;
        }

        &__btn {
            height: 40px;
            line-height:40px;
            width:100%;
            font-size: 14px;
            margin: 9px 10px;
            background-image: linear-gradient(to bottom, #ffd6e7, #f88cb6);
            border-radius: 4px;
            display: inline-block;
            text-align: center;
            color: #222;
            cursor: pointer;
            transition: color 0.12s ease-out;
            text-decoration: none;
            outline: 0;
            touch-action: manipulation;
        }
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