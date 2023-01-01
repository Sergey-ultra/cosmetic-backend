import api from '../utils/api'
import prepareQueryParams from "../utils/prepareQueryParams";

export default {
    namespaced: true,
    state: {
        allStores: [],
        stores: [],
        isLoadingStores: false,
        tableOptions: {
            page: 1,
            perPage: 20,
            sortBy:'links_count',
            sortDesc: true
        },
        total: 0,
        filterOptions: {},
        loadedStore: {}
    },
    mutations:{
        setAllStores: (state, payload) => state.allStores = [...payload],
        setTableOptions: (state, payload) => state.tableOptions = {...payload},
        setFilterOptions: (state, payload) => {
            state.filterOptions = {...payload}
            state.tableOptions = {
                page: 1,
                perPage: 20,
                sortBy: 'links_count',
                sortDesc: true
            }
        },
        setTableOptionsToDefault: state => state.tableOptions = {
            page: 1,
            perPage: 20,
            sortBy: 'links_count',
            sortDesc: true
        },
        setStores: (state, payload) => {
            state.stores = [...payload.data]
            state.total = payload.total
        },
        setIsLoadingStores: (state, payload) => state.isLoadingStores = payload,
        setLoadedStore: (state, payload) => state.loadedStore = {...payload}
    },
    actions:{
        loadAllStores: async ({ commit }) => {
            const {data} = await api.get('/stores', { params: { per_page: -1 }})
            if (data) {
                commit('setAllStores', data)
            }
        },
        reloadStores: ({ commit, dispatch }) => {
            commit('setTableOptionsToDefault')
            dispatch('loadStores')
        },
        loadStores: async ({ commit, state }) => {
            commit('setIsLoadingStores', true)

            const params = prepareQueryParams(state.tableOptions, state.filterOptions)

            const { data } = await api.get(`/stores`, { params })
            if (data) {
                commit('setStores', data)
            }
            commit('setIsLoadingStores', false)
        },
        loadItem: async({ commit }, id) =>  {
            const res = await api.get(`/stores/${id}`)
            if (res) {
                commit('setLoadedStore', res)
            }
        },
        createItem: async({ dispatch }, object) => {
            const { data } = await api.post('/stores', object)
            if (data) {
                dispatch('reloadStores')
            }
        },
        updateItem: async ({ dispatch }, object) => {
            const { data } = await api.put(`/stores/${object.id}`, object)
            if (data) {
                dispatch('loadStores')
            }
        },
        deleteItem: async ({ dispatch }, id) => {
            //await api.delete(`/stores/${id}`)

            dispatch('reloadStores')
            dispatch('notification/setSuccess', 'Удаление временно невозможно', { root: true })
        },
        changePriceParsingStatus: async({ dispatch }, obj) => {
            const { data } = await api.post(`/stores/change-price-parsing-status`, obj)
            if (data.status) {
                dispatch('notification/setSuccess', 'Статус успешно изменен', {root: true})
                dispatch('loadStores')
            }
        },
        changeCheckingImageCountStatus: async({ dispatch }, obj) => {
            const { data } = await api.post(`/stores/change-checking-image-count-status`, obj)
            if (data.status) {
                dispatch('notification/setSuccess', 'Статус успешно изменен', {root: true})
                dispatch('loadStores')
            }
        },
    }
}
