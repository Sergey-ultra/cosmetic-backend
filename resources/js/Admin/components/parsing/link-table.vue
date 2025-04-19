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

            <buttonComponent
                :size="'small'"
                class="table__top-item"
                :disabled="storeId === null || storeId === 'null'"
                @click="setTableOptionsToDefault"
            >
                <svg  xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 30 30" width="24px" height="24px">
                    <path d="M 15 3 C 12.031398 3 9.3028202 4.0834384 7.2070312 5.875 A 1.0001 1.0001 0 1 0 8.5058594 7.3945312 C 10.25407 5.9000929 12.516602 5 15 5 C 20.19656 5 24.450989 8.9379267 24.951172 14 L 22 14 L 26 20 L 30 14 L 26.949219 14 C 26.437925 7.8516588 21.277839 3 15 3 z M 4 10 L 0 16 L 3.0507812 16 C 3.562075 22.148341 8.7221607 27 15 27 C 17.968602 27 20.69718 25.916562 22.792969 24.125 A 1.0001 1.0001 0 1 0 21.494141 22.605469 C 19.74593 24.099907 17.483398 25 15 25 C 9.80344 25 5.5490109 21.062074 5.0488281 16 L 8 16 L 4 10 z"/>
                </svg>
            </buttonComponent>

            <div class="table__buttons">
                <slot name="buttons"></slot>
                <buttonComponent
                    :size="'small'"
                    :disabled="!selectedLinkIds.length"
                    @click="$emit('getItemsByLinkIds', selectedLinkIds)"
                >
                    Спарсить {{ forPrice ? 'цены' : 'продукты'}} по выбранным ссылкам
                </buttonComponent>
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
                <label class="table__item table__item-select">
                    <input type="checkbox" :value="link.id" v-model="selectedLinkIds">
                </label>
                <div class="table__item table__item-id" @click="toggleSelectedLink(link.id)">{{ link.id }}</div>
                <div class="table__item table__item-link" @click="toggleSelectedLink(link.id)">
                   <span>{{ link.link }}</span>
                </div>
                <div class="table__item table__item-external">
                    <a :href="link.link" target="_blank">
                        <svg
                            height="16px"
                            width="16px"
                            xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                            viewBox="0 0 26 26"
                            xml:space="preserve"
                        >
                            <g>
                                <path style="fill:#030104;" d="M18,17.759v3.366C18,22.159,17.159,23,16.125,23H4.875C3.841,23,3,22.159,3,21.125V9.875
                                    C3,8.841,3.841,8,4.875,8h3.429l3.001-3h-6.43C2.182,5,0,7.182,0,9.875v11.25C0,23.818,2.182,26,4.875,26h11.25
                                    C18.818,26,21,23.818,21,21.125v-6.367L18,17.759z"/>
                                <g>
                                    <path style="fill:#030104;" d="M22.581,0H12.322c-1.886,0.002-1.755,0.51-0.76,1.504l3.22,3.22l-5.52,5.519
                                        c-1.145,1.144-1.144,2.998,0,4.141l2.41,2.411c1.144,1.141,2.996,1.142,4.14-0.001l5.52-5.52l3.16,3.16
                                        c1.101,1.1,1.507,1.129,1.507-0.757L26,3.419C25.999-0.018,26.024-0.001,22.581,0z"/>
                                </g>
                            </g>
                        </svg>
                    </a>
                </div>
                <div class="table__item table__item-date">{{ link.date}}</div>
                <div v-if="link.is_body_exist" class="table__item table__item-is_body_exist">{{ link.is_body_exist}}</div>
                <div v-if="link.is_body_exist" class="table__item table__item-action">
                    <buttonComponent
                        @click="deleteBodyFromParsingLinkLocal(link.id)"
                        :color="'red'"
                        :icon="true"
                        :outline="true"
                        :round="true"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                             class="bi bi-trash-fill" viewBox="0 0 16 16">
                            <path
                                d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z"/>
                        </svg>
                    </buttonComponent>
                </div>
            </div>
        </div>

        <div class="pagination" v-if="totalCount">
            <div class="pagination__item">
                {{ paginationCountText }}
            </div>
            <buttonComponent
                :size="'small'"
                :color="'grey'"
                :outline="true"
                :disabled="options.page === 1"
                @click="minusPage"
                class="pagination__item"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     class="bi bi-chevron-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                          d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                </svg>
            </buttonComponent>
            <select v-model="options.page" class="pagination__item">
                <option v-for="(page, index) in pages" :key="index">
                    {{ page }}
                </option>
            </select>
            <buttonComponent
                :size="'small'"
                :color="'grey'"
                :outline="true"
                :disabled="options.page === lastPage"
                @click="plusPage"
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
    import {mapActions, mapMutations, mapState} from "vuex";
    import buttonComponent from "../button-component.vue"
    export default {
        name: "link-table",
        components: {
            buttonComponent
        },
        data() {
            return {
                optionsItemsPerPage:[5,10,20,30,50],
                selectedLinkIds:[],
                isSelectAllLinks:false,

            }
        },
        props: {
            storeId: {
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
            headers() {
                if  (!this.forPrice) {
                    return [
                        {title: 'id', value: 'id', width: '4%'},
                        {title: 'Ссылка', value: 'link', width: '73%'},
                        {title: 'Ext', value: 'link', width: '4%'},
                        {title: 'Дата', value: 'date', width: '5%'},
                        {title: 'Есть Body?', value: 'is_body_exist', width: '5%'},
                        {title: 'Действия', value: 'action', width: '5%'},
                    ];
                }
                return [
                    {title: 'id', value: 'id', width: '4%'},
                    {title: 'Ссылка', value: 'link', width: '73%'},
                    {title: 'Ext', value: 'link', width: '4%'},
                    {title: 'Дата', value: 'date', width: '5%'},
                ];
            },
            options: {
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
            },
            totalCount(value, oldValue) {
                if (oldValue !== 0 && value !== oldValue) {
                    this.updateCountBeforeEnd({ storeId: this.storeId, count: value })
                }
            }
        },
        unmounted() {
            this.setLinksWithPaginationToDefault()
        },
        methods: {
            ...mapActions('parsingLink', ['loadLinksWithPagination', 'deleteBodyFromParsingLink']),
            ...mapMutations('parsingLink', ['setLinksWithPaginationToDefault','setTableOptions',
                'setTableOptionsToDefault', 'updateCountBeforeEnd']),
            async deleteBodyFromParsingLinkLocal(id) {
                await this.deleteBodyFromParsingLink(id);
                await this.loadLinks();
            },
            toggleSelectedLink(id) {
                const index =  this.selectedLinkIds.indexOf(id);
                if (index === -1) {
                    this.selectedLinkIds.push(id)
                } else {
                    this.selectedLinkIds.splice(index, 1);
                }
            },
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
                this.loadLinksWithPagination(params);
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
            border-bottom: 1px solid #999999;
            min-height:35px;
            display: flex;
            &:hover {
                background: #eee;
            }
            &-header {
                background-color: #ebeff4;
            }
            &-center {
                justify-content: center;
            }
        }
        &__item {
            display: flex;
            padding: 5px 10px;
            cursor:pointer;
            &-header {
                font-weight: 600;
            }
            &-select,
            &-id {
                width: 4%;
            }
            &-link {
                width: 73%;
                display:flex;
                justify-content: space-between;
            }
            &-external {
                width: 4%;
            }
            &-is_body_exist {
                justify-content: center;
                width: 5%;
            }
            &-date {
                width: 5%;
            }
            &-action {
                justify-content: center;
                width: 5%;
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
