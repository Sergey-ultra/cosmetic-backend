import api from '../utils/api'
import prepareQueryParams from '../utils/prepareQueryParams'

export default {
    namespaced: true,
    state:{
        allCountries: [],
        loadedCountry:{},
        countriesWithPagination:[],
        isLoading: false,
        tableOptions: {
            page: 1,
            perPage: 10,
            sortBy:'',
            sortDesc: false
        },
        total: 0,
        filterOptions:{}
    },
    getters: {
        availableCountryNames: state => state.allCountries.map(el => el.name)
    },
    mutations:{
        setAllCountries: (state, payload) => state.allCountries = [...payload],
        setLoadedCountry: (state, payload) => state.loadedCountry = {...payload},
        setCountriesWithPaginations: (state, payload) => {
            state.countriesWithPagination = [...payload.data]
            state.total = payload.total
        },
        setIsLoading: (state, payload) => state.isLoading = payload,
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
    },
    actions:{
        loadAllCountries: async ({ commit }) => {
            const { data } = await api.get(`/countries`, { params: { per_page: -1 }})
            if (data) {
                commit('setAllCountries', data)
            }
        },
        reloadCountries: ({commit, dispatch}) => {
            commit('setTableOptionsToDefault')
            dispatch('loadCountriesWithPagination')
        },
        loadCountriesWithPagination: async ({ commit, state }) => {
            commit('setIsLoading', true)

            const params = prepareQueryParams(state.tableOptions, state.filterOptions)

            const { data } = await api.get(`/countries`, { params })
            if (data) {
                commit('setCountriesWithPaginations', data)
            }
            commit('setIsLoading', false)
        },
        loadItem: async({ commit }, id) =>  {
            const { data } = await api.get(`/countries/${id}`)
            if (data) {
                commit('setLoadedCountry', data)
            }
        },
        createItem: async({ dispatch }, object) => {
            const { data } = await api.post('/countries', object)
            if (data) {
                dispatch('reloadCountries')
            }
        },
        updateItem: async ({ dispatch }, object) => {
            const { data } = await api.put(`/countries/${object.id}`, object)
            if (data) {
                dispatch('reloadCountries')
            }
        },
        deleteItem: async ({ commit, dispatch }, id) => {
            //await api.delete(`/countries/${id}`)
            dispatch('reloadCountries')
            dispatch('notification/setSuccess', 'Удаление временно невозможно', { root: true })
        }
    }
}
