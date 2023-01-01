import api from '../utils/api'
import prepareQueryParams from '../utils/prepareQueryParams'

export default {
    namespaced: true,
    state:{
        currentTag:{},
        isLoadingTagTree: false,
        tagTree: [],
        availableTags: [],
        isLoadingAvailableTags: false,
    },
    getters:{
        availableTagsLocal: state => {
            return [{
                id: null,
                tag: 'Выберите родительский тег'
            }].concat(state.availableTags)
        }
    },
    mutations:{
        setIsLoadingTagTree: (state, payload) => state.isLoadingTagTree = payload,
        setIsLoadingAvailableTags: (state, payload) => state.isLoadingAvailableTags = payload,
        setTagTree: (state, payload) => state.tagTree = [...payload],
        setAvailableTags: (state, payload) => state.availableTags = [...payload],
        setCurrentTag: (state, payload) => state.currentTag = {...payload}
    },
    actions: {
        loadAvailableTags: async ({commit}) => {
            commit('setIsLoadingAvailableTags', true);
            const { data } = await api.get(`/tags`)
            if (data) {
                commit('setAvailableTags', data)
            }

            commit('setIsLoadingAvailableTags', false);
        },
        loadTagTree: async ({commit}) => {
            commit('setIsLoadingTagTree', true);
            const { data } = await api.get(`/tags/tree`)
            if (data) {
                commit('setTagTree', data)
            }

            commit('setIsLoadingTagTree', false);
        },
        loadItem: async ({commit}, id) => {
            const { data } = await api.get(`/tags/${id}`)
            if (data) {
                commit('setCurrentTag', data)
            }
        },
        createItem: async ({dispatch}, object) => {
            const {data} = await api.post('/tags', object)
            if (data) {
                dispatch('loadTagTree')
            }
        },
        updateItem: async ({dispatch}, object) => {
            const {data} = await api.put(`/tags/${object.id}`, object)
            if (data) {
                dispatch('loadTagTree')
            }
        },
        deleteItem: async ({ dispatch }, id) => {
            await api.delete(`/tags/${id}`)
            dispatch('loadTagTree')
        }

    }
}
