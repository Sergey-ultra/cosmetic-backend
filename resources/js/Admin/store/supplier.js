import api from '../utils/api'
import prepareQueryParams from "../utils/prepareQueryParams";

export default {
    namespaced: true,
    state: {
        allSuppliers: [],
        suppliersWithPagination: [],
        isLoadingSuppliers: false,
        tableOptions: {
            page: 1,
            perPage: 20,
            sortBy:'',
            sortDesc: true
        },
        total: 0,
        filterOptions: {},
        currentSupplier: {}
    },
    mutations:{
        setAllSuppliers: (state, payload) => state.allSuppliers = [...payload],
        setTableOptions: (state, payload) => state.tableOptions = {...payload},
        setFilterOptions: (state, payload) => {
            state.filterOptions = {...payload}
            state.tableOptions = {
                page: 1,
                perPage: 20,
                sortBy: '',
                sortDesc: true
            }
        },
        setTableOptionsToDefault: state => state.tableOptions = {
            page: 1,
            perPage: 20,
            sortBy: '',
            sortDesc: true
        },
        setSuppliersWithPagination: (state, payload) => {
            state.suppliersWithPagination = [...payload.data]
            state.total = payload.total
        },
        setIsLoadingSuppliers: (state, payload) => state.isLoadingSuppliers = payload,
        setCurrentSupplier: (state, payload) => state.currentSupplier = {...payload}
    },
    actions:{
        loadAllSuppliers: async ({ commit }) => {
            const {data} = await api.get('/suppliers', { params: { per_page: -1 }})
            if (data) {
                commit('setAllSuppliers', data)
            }
        },
        reloadSuppliers: ({ commit, dispatch }) => {
            commit('setTableOptionsToDefault')
            dispatch('loadSuppliersWithPagination')
        },
        loadSuppliersWithPagination: async ({ commit, state }) => {
            commit('setIsLoadingSuppliers', true)

            const params = prepareQueryParams(state.tableOptions, state.filterOptions)

            const { data } = await api.get(`/suppliers`, { params })
            if (data) {
                commit('setSuppliersWithPagination', data)
            }
            commit('setIsLoadingSuppliers', false)
        },
        loadItem: async({ commit }, id) =>  {
            const res = await api.get(`/suppliers/${id}`)
            if (res) {
                commit('setCurrentSupplier', res)
            }
        },
        setStatus: async({ dispatch }, obj) => {
            const { data } = await api.post(`/suppliers/set-status/${obj.id}`, obj)
            if (data.status === 'success') {
                dispatch('notification/setSuccess', 'Статус успешно изменен', {root: true})
                dispatch('loadSuppliersWithPagination')
            }
        },
    }
}
