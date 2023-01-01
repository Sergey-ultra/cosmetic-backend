import api from '../utils/api'
import prepareQueryParams from '../utils/prepareQueryParams'

export default {
    namespaced: true,
    state:{
        tableOptions: {
            page: 1,
            perPage: 10,
            sortBy: '',
            sortDesc: false,
        },
        total: 0,
        isLoadingQuestions: false,
        questionWithPagination: [],
        filterOptions: {
            review_status: { value: 'null' },
            rating: { value: 'null' }
        },
    },
    mutations: {
        setIsLoadingQuestions: (state, payload) => state.isLoadingQuestions = payload,
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
        setQuestionWithPagination: (state, payload) => {
            state.questionWithPagination = [...payload.data]
            state.total = payload.total
        }
    },
    actions:{
        reloadQuestions: ({ commit, dispatch }) => {
            commit('setTableOptionsToDefault')
            dispatch('loadQuestionsWithPagination')
        },
        loadQuestionsWithPagination: async({ commit, state }) => {
            commit('setIsLoadingQuestions', true)

            const params = prepareQueryParams(state.tableOptions, state.filterOptions)

            const { data } = await api.get('questions', { params })
            if (data) {
                commit('setQuestionWithPagination', data)
            }
            commit('setIsLoadingQuestions', false)
        },
        setStatus: async({ dispatch }, obj) => {
            const { data } = await api.post(`/questions/set-status/${obj.id}`, obj)
            if (data.status === 'success') {
                dispatch('notification/setSuccess', 'Статус успешно изменен', { root: true })
                dispatch('reloadQuestions')
            }
        }
    }
}
