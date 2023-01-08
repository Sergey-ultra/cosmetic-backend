import api from '../utils/api'
import prepareQueryParams from "../utils/prepareQueryParams";

export default {
    namespaced: true,
    state: {
        allVideos: [],
        videos: [],
        isLoadingVideos: false,
        tableOptions: {
            page: 1,
            perPage: 20,
            sortBy:'id',
            sortDesc: false
        },
        total: 0,
        filterOptions: {
            status: { value: 'null' },
        },
        currentVideo: {}
    },
    mutations:{
        setAllVideos: (state, payload) => state.allVideos = [...payload],
        setTableOptions: (state, payload) => state.tableOptions = {...payload},
        setFilterOptions: (state, payload) => {
            state.filterOptions = {...payload}
            state.tableOptions = {
                page: 1,
                perPage: 20,
                sortBy: 'id',
                sortDesc: false
            }
        },
        setTableOptionsToDefault: state => state.tableOptions = {
            page: 1,
            perPage: 20,
            sortBy: 'id',
            sortDesc: false
        },
        setVideos: (state, payload) => {
            state.videos = [...payload.data]
            state.total = payload.meta.total
        },
        setIsLoadingVideos: (state, payload) => state.isLoadingVideos = payload,
        setCurrentVideo: (state, payload) => state.currentVideo = {...payload}
    },
    actions:{
        loadAllVideos: async ({ commit }) => {
            const {data} = await api.get('/videos', { params: { per_page: -1 }})
            if (data) {
                commit('setAllVideos', data)
            }
        },
        reloadVideos: ({ commit, dispatch }) => {
            commit('setTableOptionsToDefault')
            dispatch('loadVideos')
        },
        loadVideos: async ({ commit, state }) => {
            commit('setIsLoadingVideos', true)

            const params = prepareQueryParams(state.tableOptions, state.filterOptions)

            const data = await api.get(`/videos`, { params })
            if (data) {
                commit('setVideos', data)
            }
            commit('setIsLoadingVideos', false)
        },
        loadItem: async({ commit }, id) =>  {
            const res = await api.get(`/videos/${id}`)
            if (res) {
                commit('setCurrentVideo', res)
            }
        },
        createItem: async({ dispatch }, object) => {
            const { data } = await api.post('/videos', object)
            if (data) {
                dispatch('reloadStores')
            }
        },
        updateItem: async ({ dispatch }, object) => {
            const { data } = await api.put(`/videos/${object.id}`, object)
            // if (data) {
            //     dispatch('loadStores')
            // }
        },
        deleteItem: async ({ dispatch }, id) => {
            //await api.delete(`/videos/${id}`)
            dispatch('reloadStores')
            dispatch('notification/setSuccess', 'Удаление временно невозможно', { root: true })
        },
        setStatus: async({ dispatch }, obj) => {
            const { data } = await api.post(`/videos/set-status/${obj.id}`, obj)
            if (data.status === 'success') {
                dispatch('notification/setSuccess', 'Статус успешно изменен', { root: true })
                dispatch('reloadVideos')
            }
        }
    }
}
