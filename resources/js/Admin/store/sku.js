import api from '../utils/api'
import prepareQueryParams from "../utils/prepareQueryParams";

export default {
    namespaced: true,
    state:{
        isLoadingCurrentSku: false,
        loadedSku:{},
        isLoading: false,
        skus: [],
        tableOptions: {
            page: 1,
            perPage: 20,
            sortBy:'id',
            sortDesc: true
        },
        total: 0,
        filterOptions:{
            category: { value: 'null' }
        }
    },
    mutations:{
        setIsLoadingCurrentSku: (state, data) => state.isLoadingCurrentSku = data,
        setLoadedSku: (state, payload) => state.loadedSku = {...payload},
        setIsLoading: (state, data) => state.isLoading = data,
        setTableOptions: (state, payload) => state.tableOptions = {...payload},
        setFilterOptions: (state, payload) => {
            state.filterOptions = {...payload}
            state.tableOptions = {
                page: 1,
                perPage: 20,
                sortBy:'id',
                sortDesc: true
            }
        },
        setTableOptionsToDefault: state => state.tableOptions = {
            page: 1,
            perPage: 20,
            sortBy:'id',
            sortDesc: true
        },
        setSkus: (state, payload) => {
            state.skus = [...payload.data]
            state.total = payload.meta.total
        },

    },
    actions:{
        reloadSkus: ({ commit, dispatch }) => {
            commit('setTableOptionsToDefault')
            dispatch('loadSkus')
        },
        loadSkus: async({ commit, state }, object = {}) => {
            commit('setIsLoading', true)

            const params = prepareQueryParams(state.tableOptions, state.filterOptions)

            const res = await api.get(`/skus`, { params })
            if (res) {
                commit('setSkus', res)
            }
            commit('setIsLoading', false)
        },
        loadItem: async({ commit }, id) =>  {
            commit('setIsLoadingCurrentSku', true)

            const { data } = await api.get(`/skus/${id}`)
            if (data) {
                commit('setLoadedSku', data)
            }
            commit('setIsLoadingCurrentSku', false)

        },
        createItem: async({ dispatch }, object) => {
            const { data } = await api.post('/skus', object)
            if (data) {
                dispatch('reloadSkus')
            }
        },
        updateItem: async ({ dispatch }, object) => {
            const { data } = await api.put(`/skus/${object.id}`, object)
            if (data) {
                dispatch('reloadSkus')
                dispatch('notification/setSuccess', 'Успешно обновлено', { root: true })
            }
        },
        deleteItem: async ({ dispatch }, id) => {
            //await api.delete(`/skus/${id}`)
            dispatch('reloadSkus')
            dispatch('notification/setSuccess', 'Удаление временно невозможно', { root: true })
        }
    }
}
