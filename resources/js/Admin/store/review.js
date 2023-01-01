import api from '../utils/api'
import prepareQueryParams from '../utils/prepareQueryParams'

export default {
    namespaced: true,
    state:{
        reviewsDynamics: 0,
        allReviews: [],
        reviews: [],
        loadedReview:{},
        tableOptions: {
            page: 1,
            perPage: 10,
            sortBy: '',
            sortDesc: false,
        },
        total: 0,
        isLoading: false,
        filterOptions: {
            review_status: { value: 'null' },
            rating: { value: 'null' }
        },
    },
    mutations:{
        setReviewsDynamics: (state, payload) => state.reviewsDynamics = payload,
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
            sortDesc: false,
        },
        setAllReviews: (state, payload) => state.allReviews = [...payload],
        setReviews: (state, payload) => {
            state.reviews = [...payload.data]
            state.total = payload.meta.total
        },
        setLoadedReview: (state, payload) => state.loadedReview = {...payload}

    },
    actions:{
        loadAllReviews: async({ commit }) => {
            const res = await api.get('/reviews',{ params: { per_page: -1 }})
            if (res) {
                commit('setAllReviews', res)
            }
        },
        loadReviewsDynamics: async ({ commit }) => {
            const { data } = await api.get('/reviews/dynamics')
            if (data) {
                commit('setReviewsDynamics', data)
            }
        },
        reloadReviews: ({commit, dispatch}) => {
            commit('setTableOptionsToDefault')
            dispatch('loadReviews')
        },
        loadReviews: async({ commit, state }) => {
            commit('setIsLoading', true)

            const params = prepareQueryParams(state.tableOptions, state.filterOptions)

            const data = await api.get(`/reviews`, { params})
            if (data) {
                commit('setReviews', data)
            }
            commit('setIsLoading', false)
        },
        loadItem: async({ commit }, id) =>  {
            const { data } = await api.get(`/reviews/${id}`)
            if (data) {
                commit('setLoadedReview', data)
            }
        },
        createItem: async({ dispatch }, object) => {
            const { data } = await api.post('/reviews', object)
            if (data) {
                dispatch('reloadReviews')
            }
        },
        updateItem: async ({ dispatch }, object) => {
            const { data } = await api.put(`/reviews/${object.id}`, object)
            if (data) {
                dispatch('reloadReviews')
            }
        },
        setReviewStatus: async({ dispatch }, obj) => {
            const { data } = await api.post(`/reviews/set-status/${obj.id}`, obj)
            if (data.status === 'success') {
                dispatch('notification/setSuccess', 'Статус успешно изменен', { root: true })
                dispatch('reloadReviews')
            }
        }
    }
}
