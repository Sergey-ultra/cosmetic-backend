<template>
    <div class="table">
        <div class="table__top">
            <div class="table__top-item">
                Строк на странице:
                <select  v-model="options.pageSize"  class="table__select">
                    <option v-for="(option, index) in optionsItemsPerPage" :key="index">
                        {{ option }}
                    </option>
                </select>
            </div>

            <button type="button" class="button table__top-item" @click="setTableOptionsToDefault">Reload</button>

            <div class="table__buttons">
                <slot name="buttons"></slot>
                <button
                        type="button"
                        class="button"
                        :disabled="!selectedLinkIds.length"
                        @click="$emit('getItemsByLinkIds', selectedLinkIds)"
                >
                    Спарсить {{ forPrice ? 'цены' : 'продукты'}} по выбранным ссылкам
                </button>
            </div>

        </div>

        <div class="table__wrapper">
            <div class="table__row table__row-header">
                <div class="table__item table__item-select">
                    <input type="checkbox"  v-model="isSelectAllLinks"/>
                </div>
                <div
                        v-for="header in headers"
                        :style="`width:${header.width}`"
                        :key="header.value"
                        class="table__item table__item-header"
                        @click="setSortBy(header.value)"
                >
                    {{ header.title }}
                    <div
                            v-if="options.sortBy === header.value"
                            class="sort-arrow"
                            :class="{'rotate':options.sortDesc === true}"
                    >
                               <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-arrow-up-short" viewBox="0 0 20 20">
                                   <path fill-rule="evenodd"
                                         d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.146 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 5.707V11.5a.5.5 0 0 0 .5.5z"/>
                                </svg>
                    </div>
                </div>
            </div>
            <div v-if="!links.length" class="table__row table__row-center">
                <span>Нет данных</span>
            </div>

            <div
                    class="table__row"
                    v-for="link in links"
                    :key="link.id"
            >
                <div class="table__item table__item-select">
                    <input type="checkbox" :value="link.id" v-model="selectedLinkIds">
                </div>
                <div class="table__item table__item-id">{{ link.id }}</div>
                <div class="table__item table__item-link">{{ link.link }}</div>
                <div class="table__item table__item-date">{{ link.date}}</div>
            </div>
        </div>

        <div class="pagination">
            <div class="pagination__item">
                {{ paginationCountText }}
            </div>
            <div @click="minusPage" class=" pagination__item pagination__item-button">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     class="bi bi-chevron-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                          d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                </svg>
            </div>
            <select v-model="options.page" class="pagination__item">
                <option v-for="(page, index) in pages" :key="index">
                    {{ page }}
                </option>
            </select>
            <div @click="plusPage" class="pagination__item pagination__item-button">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     class="bi bi-chevron-right" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                          d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                </svg>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapActions, mapMutations, mapState} from "vuex";
    export default {
        name: "link-table",
        data() {
            return {
                optionsItemsPerPage:[5,10,20,30,50],
                headers: [
                    {title: 'id', value: 'id', width: '4%'},
                    {title: 'Ссылка', value: 'link', width: '75%'},
                    {title: 'Дата', value: 'date', width: '17%'},
                ],

                selectedLinkIds:[],
                isSelectAllLinks:false,

            }
        },
        props:{
            storeId:{
               default:null
            },
            isReloadLinks: {
                type: Boolean,
                default: false
            },
            forPrice: {
                type: Boolean,
                default: false
            }
        },
        computed: {
            ...mapState('parsingLink', ['links', 'totalCount', 'tableOptions']),
            options:{
                get() {
                    return this.tableOptions
                },
                set(value) {
                    this.setTableOptions(value)
                }
            },
            lastPage() {
                return Math.ceil(this.totalCount / this.options.pageSize)
            },
            pages() {
                return Array.from({length: this.lastPage}, (v, k) => k + 1)
            },
            paginationCountText() {
                return `${this.totalCount === 0 ? 0 : this.options.pageSize *(this.options.page - 1) + 1}
                 -
                ${this.options.pageSize * this.options.page < this.totalCount ? this.options.pageSize * this.options.page : this.totalCount}
                из
                ${this.totalCount}`
            },
        },
        watch: {
            tableOptions: {
                deep: true,
                handler(value) {
                    this.isSelectAllLinks = false
                    this.selectedLinkIds = []
                    this.loadLinks()
                }
            },
            isReloadLinks(value) {
                if (value) {
                    this.setTableOptionsToDefault()
                    this.$emit('update:isReloadLinks', false)
                }
            },
            storeId(value) {
                if (value) {
                    this.isSelectAllLinks = false
                    this.setTableOptionsToDefault()
                }
            },
            isSelectAllLinks(value) {
                if (value) {
                    this.selectedLinkIds = this.links.map(el => el.id)
                } else {
                    this.selectedLinkIds = []
                }
            }
        },
        created() {

        },
        unmounted() {
            this.setLinksWithPaginationToDefault()
        },
        methods: {
            ...mapActions('parsingLink', ['loadLinksWithPagination']),
            ...mapMutations('parsingLink', ['setLinksWithPaginationToDefault','setTableOptions', 'setTableOptionsToDefault']),
            setSortBy(value) {
                if (this.options.sortBy === value) {
                    if (this.options.sortDesc === false) {
                        this.options.sortDesc = true
                    } else {
                        this.options.sortBy = ''
                        this.options.sortDesc = false
                    }
                } else {
                    this.options.sortBy = value
                    this.options.sortDesc = false
                }
            },
            loadLinks() {
                let params = {store_id: this.storeId}
                if (this.forPrice) {
                    params.forPrice = true
                }
               this.loadLinksWithPagination(params)
            },
            plusPage() {
                if (this.options.page < this.lastPage) {
                    this.options.page++
                }
            },
            minusPage() {
                if (this.options.page > 1) {
                    this.options.page--
                }
            },
        }
    }
</script>

<style lang="scss" scoped>
    .sort-arrow {
        width:20px;
        height:20px;
        display:flex;
        align-items:center;
        justify-content: center;
    }
    .rotate {
        transition: all 0.3s ease;
        transform: rotate(-180deg);
    }
    .button {
        min-width: 28px;
        padding: 0 12.4px;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 4px;
        color: #fff;
        height: 28px;
        background: rgb(24, 103, 192);
        border: none;
        &[disabled] {
            background-color: rgba(0,0,0,.12);
            color: rgba(0,0,0,.26);
        }

    }
    .table {
        background-color: #fff;
        border: 2px solid #999999;
        border-radius: 8px;
        box-shadow: 0 1px 2px 0 rgba(0,0,0,0.16);
        overflow:hidden;
        display:flex;
        flex-direction: column;
        margin: 20px auto;
        width:100%;
        &__top {
            display:flex;
            align-items:center;
            background-color: #ebeff4;
            padding: 10px 10px 5px 10px;
            &-item {
                margin-right: 15px;
            }
        }
        &__buttons {
            display: flex;
            align-items: center;
            margin-left: auto;
        }
        &__row {
            padding: 5px 0;
            border-bottom: 1px solid #999999;
            min-height:30px;
            display: flex;
            align-items:center;
            &-header {
                background-color: #ebeff4;
            }
            &-center {
                justify-content: center;
            }
        }
        &__item {
            display: flex;
            padding: 0 10px;
            cursor:pointer;
            &-header {
                font-weight: 600;
            }
            &-select,
            &-id{
                width: 4%;
            }
            &-link {
                width: 75%;
            }
            &-date {
                width: 17%;
            }
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
                border: 1px solid #d5dae0;
                border-radius: 2px;
                width: 32px;
                height: 100%;
                display:flex;
                justify-content: center;
                align-items:center;
            }
        }
    }

    .view__img {
        height: 600px;
        width: 600px;
        & img {
            max-height: 100%;
            max-width: 100%;
        }
    }
</style>