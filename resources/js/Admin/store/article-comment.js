import api from '../utils/api'
import prepareQueryParams from "../utils/prepareQueryParams";

export default {
    namespaced: true,
    state:{
        comments: [],
        isLoadingComments: false,
        tableOptions: {
            page: 1,
            perPage: 20,
            sortBy: '',
            sortDesc: false
        },
        total: 0,
        filterOptions: {}
    },
    mutations:{
        setIsLoadingComments: (state, payload) => state.isLoadingComments = payload,
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
        setComments: (state, payload) => {
            state.comments = [...payload.data]
            state.total = payload.total
        }
    },
    actions:{
        reloadArticleComments: ({commit, dispatch}) => {
            commit('setTableOptionsToDefault')
            dispatch('loadArticleComments')
        },
        loadArticleComments: async({ commit, state }, object = {}) => {
            commit('setIsLoadingComments', true)

            const params = prepareQueryParams(state.tableOptions, state.filterOptions)

            const { data } = await api.get(`/article-comments`, { params })
            if (data) {
                commit('setComments', data)
            }
            commit('setIsLoadingComments', false)
        },
        deleteItem: async ({ dispatch }, id) => {
            await api.delete(`/article-comments/${id}`)

            dispatch('reloadArticleComments')
            dispatch('notification/setSuccess', 'Успешно удалено', { root: true })
        },
        setStatus: async({ dispatch }, obj) => {
            const { data } = await api.post(`/article-comments/set-status/${obj.id}`, obj)
            if (data.status) {
                dispatch('notification/setSuccess', 'Статус успешно изменен', { root: true })
                dispatch('loadArticleComments')
            }
        }
    }
}
