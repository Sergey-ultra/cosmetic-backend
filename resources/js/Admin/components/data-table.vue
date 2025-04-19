<template>
    <div  class="table">
        <div class="table__top">
            <div class="table__topItem">
                Строк на странице:
                <select  v-model="perPage"  class="table__select">
                    <option v-for="(option, index) in optionsItemsPerPage" :key="index">
                        {{ option }}
                    </option>
                </select>
            </div>

            <buttonComponent class="table__topItem" @click="$emit('reloadTable')" :size="'small'">
                <svg fill="currentColor" viewBox="0 0 30 30" width="24px" height="24px">
                    <path d="M 15 3 C 12.031398 3 9.3028202 4.0834384 7.2070312 5.875 A 1.0001 1.0001 0 1 0 8.5058594 7.3945312 C 10.25407 5.9000929 12.516602 5 15 5 C 20.19656 5 24.450989 8.9379267 24.951172 14 L 22 14 L 26 20 L 30 14 L 26.949219 14 C 26.437925 7.8516588 21.277839 3 15 3 z M 4 10 L 0 16 L 3.0507812 16 C 3.562075 22.148341 8.7221607 27 15 27 C 17.968602 27 20.69718 25.916562 22.792969 24.125 A 1.0001 1.0001 0 1 0 21.494141 22.605469 C 19.74593 24.099907 17.483398 25 15 25 C 9.80344 25 5.5490109 21.062074 5.0488281 16 L 8 16 L 4 10 z"/>
                </svg>
            </buttonComponent>

            <div class="table__buttons">
                <slot name="add">

                </slot>
            </div>
        </div>

        <div class="table__inner">
            <table class="table__wrapper">
            <thead class="table__thead">
                <tr>
                    <th
                            class="table__header"
                            v-for="header in headers"
                            :style="`width:${header.width}`"
                            :key="header.value"
                            :class="{
                                'active':options.sortBy === header.value,
                                'table__header-cursor': getSortable(header)
                             }"
                            @click="getSortable(header) ? setSortParams(header.value) : ''"
                    >
                        {{ header.title }}
                        <span  v-if="getSortable(header)">
                               <svg
                                       class="sort__arrow"
                                       :class="{'rotate':options.sortDesc === true}"
                                       xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                        viewBox="0 0 16 16">
                                   <path fill-rule="evenodd"
                                         d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.146 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 5.707V11.5a.5.5 0 0 0 .5.5z"/>
                                </svg>
                        </span>
                    </th>
                </tr>
            </thead>
            <thead class="table__thead">
                <tr>
                    <th
                        v-for="header in headers"
                        :style="`width:${header.width}`"
                        :key="header.value"
                    >
                        <input
                            v-if="header.filter && header.filter.type === 'input'"
                            class="table__search"
                            :value="filter[header.value] ? filter[header.value].value : null"
                            @input="$emit('update:filter', { ...this.filter, [header.value]:  { value: $event.target.value, type: 'like' }})"
                            :placeholder="`Поиск по ${header.title}`"
                        />
                        <input
                            v-if="header.filter && header.filter.type === 'checkbox'"
                            type="checkbox"
                        />
                        <select
                            v-if="header.filter && header.filter.type === 'select' && availableOptions[header.value].length"
                            :value="filter[header.value].value"
                            @input="$emit('update:filter', { ...this.filter, [header.value]:  { value: $event.target.value, type: 'strong' }})"
                        >
                            {{ availableOptions }}
                            <option value="null">Выберите</option>
                            <option
                                v-for="option in availableOptions[header.value]"
                                :key="option.title"
                                :value="option.value"
                            >
                                {{ option.title }}
                            </option>
                        </select>
                    </th>
                </tr>
            </thead>
            <thead >
                <tr class="progress">
                    <th colspan="0">
                        <div  v-if="!isLoading" style="height: 4px; width:100%;" class="progress__linear">
                            <div class="progress__linear-background"></div>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody class="table__body">
            <tr v-for="item in items" :key="item.id">
                <th
                    v-for="header in headers"
                    :key="header.value"
                    :style="`width:${header.width}`"
                    :class="{'actions': header.value === 'action'}"
                >
                    <slot
                        v-if="header.value !== 'action'"
                        :name="header.value"
                        :item="item"
                    >
                        {{ item[header.value] }}
                    </slot>
                    <div v-else class="actions__wrapper">
                        <slot
                            name="action"
                            :item="item"
                        ></slot>
                    </div>
                </th>
            </tr>
            </tbody>
        </table>
            <div class="table__layer" v-if="isLoading">
                <loader class="loader"/>
            </div>
        </div>


        <div class="pagination">
            <div class="pagination__item">{{ paginationCountText }}</div>

            <buttonComponent
                :size="'small'"
                :color="'grey'"
                :outline="true"
                :disabled="page === 1"
                @click="minus"
                class="pagination__item"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     class="bi bi-chevron-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                          d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                </svg>
            </buttonComponent>
            <select v-model="page" class="pagination__item">
                <option v-for="(page, index) in pages" :key="index">
                    {{ page }}
                </option>
            </select>
            <buttonComponent
                :size="'small'"
                :color="'grey'"
                :outline="true"
                :disabled="page === lastPage"
                @click="plus"
                class="pagination__item"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     class="bi bi-chevron-right" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                          d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                </svg>
            </buttonComponent>
        </div>
    </div>
</template>

<script>
    import loader from './loader.vue'
    import buttonComponent from "./button-component.vue";

    export default {
        name: "data-table",
        components: {
            loader,
            buttonComponent
        },
        props: {
            optionsItemsPerPage: Array,
            total: Number,
            options: {
                type: Object,
                default:() => {}
            },
            items: Array,
            headers: Array,
            isLoading: Boolean,
            filter: {
                type: Object,
                default: () => {}
            },
            availableOptions: {
                type: Object,
                default: () => {}
            }

        },
       // emits: ['update:options'],
        // setup(props, { emit }){
        //
        //
        // },
        created(){
           //console.log(this.options)
        },
        computed: {
            page: {
                get() {
                    return this.options.page
                },
                set(value) {
                   this.$emit('update:options', { ...this.options, page: value })
                }
            },
            perPage: {
                get() {
                    return this.options.perPage
                },
                set(value) {
                    console.log(value)
                    this.$emit('update:options', { ...this.options, perPage: value })
                }
            },
            lastPage() {
                return Math.ceil(this.total / this.options.perPage)
            },
            pages() {
                return Array.from({ length: this.lastPage }, (v, k) => k + 1)
            },
            paginationCountText() {
                return `${ this.total === 0 ? 0 : this.options.perPage *(this.options.page - 1) + 1}
                -
                ${ this.options.perPage * this.options.page < this.total ? this.options.perPage * this.options.page : this.total }
                из
                ${ this.total }`
            }
        },
        methods:{
            setSortParams(value) {
                let sortBy = value
                let sortDesc
                if (this.options.sortBy === value) {
                    if (this.options.sortDesc === false) {
                        sortDesc = true
                    } else {
                        sortBy = ''
                        sortDesc = false
                    }
                } else {
                    sortBy = value
                    sortDesc = false
                }
                this.$emit('update:options', { ...this.options, sortBy, sortDesc})
            },
            getSortable(header) {
                return header.value !== 'action' && (header.hasOwnProperty('sort') ? header.sort : true)
            },
            plus() {
                if (this.page < this.lastPage) {
                    this.page++
                }
            },
            minus() {
                if (this.page > 1) {
                    this.page--
                }
            },
        }
    }
</script>

<style lang="scss" scoped>
    thead {
        border-spacing: 0;
    }
    .table {
        border-radius: 8px;
        box-shadow: 0 1px 2px 0 rgba(0,0,0,0.16);
        background-color: #fff;
        overflow:hidden;
        display:flex;
        flex-direction: column;
        margin: 20px auto;
        border: 1px solid #999999;
        width:100%;
        &__top {
            display:flex;
            align-items:center;
            background-color: #ebeff4;
            padding: 10px 10px 5px 10px;
        }
        &__topItem {
            margin-right: 10px;

            &-reload {
                cursor: pointer;
            }
        }
        &__select {
            height:100%;
        }
        &__search {
            width: 100%;
            display:flex;
            align-items: center;
            justify-content: center;
            border:none;
            height:32px;
            border-radius:16px;
            background: #fff;
            padding:0 15px;
        }
        &__search:focus {
            border: 2px solid #2c509a;
        }
        &__buttons {
            margin-left: auto;
            display: flex;
            align-items: center;
        }
        &__inner {
            overflow-y: auto;
            position: relative;
        }
        &__layer {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: hsla(0,0%,100%,.81);
            display: flex;
            justify-content: center;
            align-items:center;
        }
        &__wrapper {
            color: rgba(0,0,0,.87);
            width:100%;
            border-collapse: collapse;
            border-spacing: 0;
        }
        &__thead  {
            color:rgba(0,0,0,.6);
            background-color: #ebeff4;
        }
        &__header {
            &-cursor {
                cursor: pointer;
                &:hover {
                    color: rgba(0,0,0,.87);
                }
            }
        }
        &__body tr th {
            font-weight:500;
            color: #000;
        }
    }

    .loader {
        width: 100px;
        height: 100px;
    }

    .progress {
        height: auto !important;
        &  th {
            height: auto !important;
            border: none !important;
            padding: 0;
            position: relative;
        }
        &__linear {
            width: 100%;
            background: transparent;
            overflow: hidden;
            left: 0;
            z-index: 1;
            position:absolute;
            display:inline-block;
            opacity: 0.35;

            &-background {
                opacity: 0.3;
                left: 0%;
                width: 100%;
                background-color: #1867c0 !important;
                border-color: #1867c0 !important;
                bottom: 0;
                position: absolute;
                top: 0;
                transition: inherit;
            }
        }
    }



    th, td {
        text-overflow: ellipsis;
        padding: 5px 10px;
        overflow:hidden;
    }
    .sort__arrow {
        height:24px;
        width: 24px;
        opacity:0;
        transform: rotate(0deg);
        transition: all .3s ease-in ;
    }
    .rotate {
        transform: rotate(180deg);
    }
    .active {
        color: rgba(0,0,0,.87);
    }
    .active .sort__arrow {
        opacity:1;
    }


    th:hover .sort__arrow {
        opacity:1;
    }

    tbody {
        width:100%;
        & tr {
           // display:block;
            width: 100%;
            height:35px;
            border-bottom: thin solid rgba(0,0,0,.12);
            &:hover{
                background: #eee;
            }
        }
    }

    .actions {
        overflow: visible;
        &__wrapper {
            display:flex;
            justify-content: center;
        }
    }

    .pagination {
        height: 40px;
        padding: 5px 10px;
        background-color: #ebeff4;
        display: flex;
        align-items:center;

        &__item {
            margin-right:10px;
            &-button {
                cursor: pointer;
            }
        }
    }

</style>
