import api from '../utils/api'
import {defineStore} from "pinia";

export const useProductStore = defineStore({
    id: 'product',
    state: () => ({
        currentCategory: null,
        viewMode: 'compact',

        isLoadingProductsWithPagination: false,
        productsWithPagination:[],
        totalCount:0,
        lastPage: 0,

        currentPage: 1,
        sortOption: 'popular',
        filterOptions: {
            min_price: "",
            max_price: "",
            brand_ids: [],
            category_ids: [],
            volumes: [],
            purposes:[],
            active_ingredients_group_ids: [],
            extract_ids:[],
            country_ids:[]
        },
        productLoadingMode: null,
        isLoadMore: false
    }),
    getters: {
        filterOptionsKeys(state) {
            return Object.keys(state.filterOptions);
        },
        isEmptyFilterOptions(state) {
            for (let [key, value] of Object.entries(state.filterOptions)) {
                if (key !== 'min_price' && key !== 'max_price' && value.length) {
                    return false
                } else if ((key === 'min_price' || key === 'max_price') && value !== "") {
                    return false
                }
            }
            return true
        },
        queryParams(state)  {
            let params = {}

            if (state.productLoadingMode !== null) {
                Object.assign(params, { ...state.productLoadingMode })
            }

            if (!this.isEmptyFilterOptions) {
                Object.assign(params, { ...state.filterOptions })

                if (!state.filterOptions.min_price) {
                    delete params.min_price
                }
                if (!state.filterOptions.max_price) {
                    delete params.max_price
                }
            }

            return params
        }
    },
    actions: {
        setIsLoadMore(payload) {
            this.isLoadMore = payload;
        },
        setProductLoadingMode(payload) {
            this.productLoadingMode = { ...payload };
        },
        setViewMode(payload) {
            this.viewMode = payload;
        },
        setCurrentPage(payload) {
            this.currentPage = payload;
        },
        setCurrentPageToDefault() {
            this.currentPage = 1;
        },
        setProductParamsByProductQuery(query) {
            this.filterOptions = {
                min_price: "",
                max_price: "",
                brand_ids: [],
                category_ids: [],
                volumes: [],
                purposes:[],
                active_ingredients_group_ids: [],
                extract_ids:[],
                country_ids:[]
            }

            this.sortOption = 'popular'
            this.currentPage = 1

            for (let [key, value] of Object.entries(query)) {
                if (key === 'page') {
                    this.currentPage = Number(value)

                } else if (key === 'sort') {
                    this.sortOption = value

                } else if ((key === 'min_price' || key === 'max_price') && value !== "") {
                    this.filterOptions[key] = value
                } else if (Object.keys(this.filterOptions).includes(key)) {
                    if (!Array.isArray(value)) {
                        value = [value]
                    }
                    this.filterOptions[key] = value
                }
            }
        },
        setProductFilter(payload) {
            if (payload === null) {
                this.filterOptions = {
                    min_price: "",
                    max_price: "",
                    brand_ids: [],
                    category_ids: [],
                    volumes: [],
                    purposes:[],
                    active_ingredients_group_ids: [],
                    extract_ids:[],
                    country_ids:[]
                }
            } else {
                this.filterOptions = {
                    min_price: payload.min_price,
                    max_price: payload.max_price,
                    brand_ids: [...payload.brand_ids],
                    category_ids: [...payload.category_ids],
                    volumes: [...payload.volumes],
                    purposes:[...payload.purposes],
                    active_ingredients_group_ids: [...payload.active_ingredients_group_ids],
                    extract_ids: [...payload.extract_ids],
                    country_ids: [...payload.country_ids],
                }
            }

            this.currentPage = 1
        },
        async loadProductsWithPagination(object = {}) {
            this.isLoadingProductsWithPagination = true;
            let params = { ...this.queryParams };

            if (this.sortOption !== 'popular') {
                params.sort = this.sortOption;
            }

            params.page = this.currentPage;


            const res = await api.get(`/skus/main`, {params})
            if (res) {
                if (this.isLoadMore) {
                    this.productsWithPagination = this.productsWithPagination.concat(res.products.data);
                    this.isLoadMore = false;
                } else {
                    this.productsWithPagination = [...res.products.data];
                    this.totalCount = res.products.total;
                    this.lastPage = res.products.last_page;
                    this.currentCategory = { ...res.category };
                }
            }
            this.isLoadingProductsWithPagination = false;
        }
    }
});
