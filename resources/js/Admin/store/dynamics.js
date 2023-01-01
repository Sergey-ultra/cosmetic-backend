import api from '../utils/api'
import prepareQueryParams from "../utils/prepareQueryParams";

export default {
    namespaced: true,
    state:{
        trackingsWithPagination:[],
        isLoading: false,
        tableOptions: {
            page: 1,
            perPage: 10,
            sortBy:'',
            sortDesc: false
        },
        filterOptions: {

        },
        total: 0,
        trackingDynamics: [],
        visitStatistics: [],
        linkClicksByStoresStatistics: [],
        linkClicksByDateStatistics: [],
        ratingStatistics: []
    },
    mutations:{
        setTrackingsWithPaginations: (state, payload) => {
            state.trackingsWithPagination = [...payload.data]
            state.total = payload.total
        },
        setIsLoading: (state, payload) => state.isLoading = payload,
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
            perPage: 10,
            sortBy: '',
            sortDesc: false
        },
        setTrackingDynamics: (state, payload) => state.trackingDynamics = [...payload],
        setVisitStatistics: (state, payload) => state.visitStatistics = [...payload],
        setLinkClicksByStoreStatistics: (state, payload) => state.linkClicksByStoresStatistics = [...payload],
        setLinkClicksByDateStatistics: (state, payload) => state.linkClicksByDateStatistics = [...payload],
        setRatingStatistics: (state, payload) => state.ratingStatistics = [...payload]
    },
    actions:{
        loadTrackingsWithPagination: async ({ commit, state }) => {
            commit('setIsLoading', true)

            const params = prepareQueryParams(state.tableOptions, state.filterOptions)

            const res = await api.get(`/trackings`, { params })
            if (res) {
                commit('setTrackingsWithPaginations', res)
            }
            commit('setIsLoading', false)
        },
        loadTrackingDynamics: async({ commit }) => {
            const { data } = await api.get(`/trackings/dynamics`)

            if (data) {
                commit('setTrackingDynamics', data)
            }
        },
        loadVisitStatistics: async({ commit }) => {
            const { data } = await api.get(`/visit-statistics`)
            if (data) {
                commit('setVisitStatistics', data)
            }
        },
        loadLinkClicksByStoresStatistics: async({ commit }) => {
            const { data } = await api.get(`/links/by-store`)
            if (data) {
                commit('setLinkClicksByStoreStatistics', data)
            }
        },
        loadLinkClicksByDateStatistics: async({ commit }) => {
            const { data } = await api.get(`links/by-date`)
            if (data) {
                commit('setLinkClicksByDateStatistics', data)
            }
        },
        loadRatingStatistics: async({ commit }) => {
            const { data } = await api.get('/rating/dynamic')
            if (data && Array.isArray(data)) {
                commit('setRatingStatistics', data)
            }
        }
    }
}
