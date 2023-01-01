import api from '../utils/api'
import prepareQueryParams from "../utils/prepareQueryParams";

export default {
    namespaced: true,
    state:{
        allBrands:[],
        brands: [],
        isLoadingCurrentBrand: false,
        loadedBrand:{},
        tableOptions: {
            page: 1,
            perPage: 20,
            sortBy:'',
            sortDesc: false
        },
        filterOptions: {
            country: { value: 'null' }
        },
        total: 0,
        isLoading: false,
    },
    mutations:{
        setIsLoading: (state, payload) => state.isLoading = payload,
        setIsLoadingCurrentBrand: (state, payload) => state.isLoadingCurrentBrand = payload,
        setTableOptions: (state, payload) => state.tableOptions = {...payload},
        setFilterOptions: (state, payload) => {
            state.filterOptions = {...payload}
            state.tableOptions = {
                page: 1,
                perPage: 20,
                sortBy: '',
                sortDesc: false
            }
        },
        setTableOptionsToDefault: state => state.tableOptions = {
            page: 1,
            perPage: 20,
            sortBy: '',
            sortDesc: false
        },
        setAllBrands: (state, payload) => state.allBrands = [...payload],
        setBrands: (state, payload) => {
            state.brands = [...payload.data]
            state.total = payload.total
        },
        setLoadedBrand: (state, payload) => state.loadedBrand = {...payload},
    },
    actions:{
        loadAllBrands: async({ commit }) => {
            const { data } = await api.get('/brands', { params: { per_page: -1 }})
            if (data){
                commit('setAllBrands', data)
            }
        },
        reloadBrands: ({ commit, dispatch }) => {
            commit('setTableOptionsToDefault')
            dispatch('loadBrands')
        },
        loadBrands: async({ commit, state }) => {
            commit('setIsLoading', true)

            const params = prepareQueryParams(state.tableOptions, state.filterOptions)

            const { data } = await api.get(`/brands`, { params })
            if (data) {
                commit('setBrands', data)
            }
            commit('setIsLoading', false)
        },
        loadItem: async({ commit }, id) =>  {
            commit('setIsLoadingCurrentBrand', true)

            const { data } = await api.get(`/brands/${id}`)
            if (data) {
                commit('setLoadedBrand', data)
            }

            commit('setIsLoadingCurrentBrand', false)
        },
        createItem: async({ dispatch }, object) => {
            const { data } = await api.post('/brands', object)
            if (data) {
                dispatch('reloadBrands')
            }
        },
        updateItem: async ({ dispatch }, object) => {
            const { data } = await api.put(`/brands/${object.id}`, object)
            if (data) {
                dispatch('reloadBrands')
            }
        },
        deleteItem: async ({ dispatch }, id) => {
            //await api.delete(`/brands/${id}`)
            dispatch('reloadBrands')
            dispatch('notification/setSuccess', 'Удаление временно невозможно', { root: true })

        }
    }
}
