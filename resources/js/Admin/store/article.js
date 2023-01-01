import api from '../utils/api'
import prepareQueryParams from "../utils/prepareQueryParams";

export default {
    namespaced: true,
    state:{
        allArticles: [],
        articlesWithPagination:[],
        isLoading: false,
        tableOptions: {
            page: 1,
            perPage: 10,
            sortBy:'',
            sortDesc: false
        },
        total: 0,
        filterOptions: {
            category_name: { value: 'null' }
        },
        currentArticle:{},
        articleCategories: []
    },
    getters: {
        availableArticleCategoryNames: state => state.articleCategories.map(el => el.name)
    },
    mutations:{
        setAllArticles: (state, payload) => state.allArticles = [...payload],
        setArticlesWithPagination: (state, payload) => {
            state.articlesWithPagination = [...payload.data]
            state.total = payload.meta.total
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
        setCurrentArticle: (state, payload) => state.currentArticle = {...payload},
        setArticleCategories: (state, payload) => state.articleCategories = [...payload]
    },
    actions:{
        loadArticleCategories: async ({ commit }) => {
            const { data } = await api.get(`/articles/categories`)
            if (data && Array.isArray(data)) {
                commit('setArticleCategories', data)
            }
        },
        loadAllArticles: async ({ commit }) => {
            const res = await api.get(`/articles`, { params: { per_page: -1 }})
            if (res.length) {
                commit('setAllArticles', res)
            }
        },
        reloadArticles: ({ commit, dispatch }) => {
            commit('setTableOptionsToDefault')
            dispatch('loadArticlesWithPagination')
        },
        loadArticlesWithPagination: async ({ commit, state }) => {
            commit('setIsLoading', true)

            const params = prepareQueryParams(state.tableOptions, state.filterOptions)

            const res = await api.get(`/articles`, { params })
            if (res) {
                commit('setArticlesWithPagination', res)
            }
            commit('setIsLoading', false)
        },
        loadItem: async({ commit }, id) =>  {
            const { data } = await api.get(`/articles/${id}`)
            if (data) {
                commit('setCurrentArticle', data)
            }
        },
        createItem: async({ dispatch }, object) => {
            const { data } = await api.post('/articles', object)
            if (data.status) {
                dispatch('notification/setSuccess', 'Успешно создано', { root: true })
            }
        },
        publishItem: async({ dispatch }, id) => {
            const { data } = await api.post(`/articles/publish/${id}`)
            if (data.status) {
                dispatch('notification/setSuccess', 'Успешно опубликовано', { root: true })
                dispatch('reloadArticles')
            }
        },
        withdrawFromPublication: async({ dispatch }, id) => {
            const { data } = await api.post(`/articles/withdraw-from-publication/${id}`)
            if (data.status) {
                dispatch('notification/setSuccess', 'Успешно убрано с публикации', { root: true })
                dispatch('reloadArticles')
            }
        },
        updateItem: async ({ dispatch }, object) => {
            const { data } = await api.put(`/articles/${object.id}`, object)
            if (data) {
                dispatch('notification/setSuccess', 'Успешно обновлено', { root: true })
            }
        },
        deleteItem: async ({ dispatch }, id) => {
            await api.delete(`/articles/${id}`)
            //dispatch('notification/setSuccess', 'Удаление временно невозможно', { root: true })
            dispatch('reloadArticles')
        }
    }
}
