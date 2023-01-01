import api from '../utils/api'
import prepareQueryParams from "../utils/prepareQueryParams";

export default {
    namespaced: true,
    state: {
        allCategories: [],
        isLoading: false,
        categories: [],
        currentCategory:{},
        isLoadingCurrentCategory: false,
        tableOptions: {
            page: 1,
            perPage: 10,
            sortBy: '',
            sortDesc: false
        },
        total: 0,
        filterOptions: {}
    },
    getters: {
        availableCategoryNames: state => state.allCategories.map(el => el.name)
    },
    mutations: {
        setIsLoading: (state, data) => state.isLoading = data,
        setTableOptions: (state, payload) => state.tableOptions = {...payload},
        setFilterOptions: (state, payload) => {
            state.filterOptions = {...payload}
            state.tableOptions = {
                page: 1,
                perPage: 10,
                sortBy: '',
                sortDesc: false
            }
        },
        setTableOptionsToDefault: state => state.tableOptions = {
            page: 1,
            perPage: 10,
            sortBy: '',
            sortDesc: false
        },
        setAllCategories: (state, payload) => state.allCategories = [...payload],
        setCategories: (state, payload) => {
            state.categories = [...payload.data]
            state.total = payload.total
        },
        setCurrentCategory: (state, payload) => state.currentCategory = { ...payload },
        setIsLoadingCurrentCategory: (state, payload) => state.isLoadingCurrentCategory = payload,
    },
    actions:{
        loadAllCategories: async({ commit }) => {
            const { data } = await api.get('/categories', { params: { per_page: -1 }})
            if (data) {
                commit('setAllCategories', data)
            }
        },
        reloadCategories: ({ commit, dispatch }) => {
            commit('setTableOptionsToDefault')
            dispatch('loadCategories')
        },
        loadCategories: async({ commit, state }, object = {}) => {
            commit('setIsLoading', true)

            const params = prepareQueryParams(state.tableOptions, state.filterOptions)


            const { data } = await api.get('/categories', { params })
            if (data) {
                commit('setCategories', data)
            }
            commit('setIsLoading', false)
        },
        loadItem: async({ commit }, id) =>  {
            commit('setIsLoadingCurrentCategory', true)

            const { data } = await api.get(`/categories/${id}`)
            if (data) {
                commit('setCurrentCategory', data)
            }
            commit('setIsLoadingCurrentCategory', false)
        },
        createItem: async({ dispatch }, object) => {
            const { data } = await api.post('/categories', object)
            if (data) {
                dispatch('reloadCategories')
            }
        },
        updateItem: async ({ dispatch }, object) => {
            const { data } = await api.put(`/categories/${object.id}`, object)
            if (data) {
                dispatch('reloadCategories')
            }
        },
        deleteItem: async ({ dispatch }, id) => {
            //await api.delete(`/categories/${id}`)

            dispatch('reloadCategories')
            dispatch('notification/setSuccess', 'Удаление временно невозможно', { root: true })
        }
    }
}



