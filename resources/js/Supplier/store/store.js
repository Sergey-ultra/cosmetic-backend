import api from '../utils/api'

export default {
    namespaced: true,
    state: {
        myStore: null,
        allStores: [],
        isLoadingMyStore: false,
        isLoadingAllStores: false,
        isAddingPriceFile: false
    },
    mutations: {
        setIsLoadingAllStores: (state, payload) => state.isLoadingAllStores = payload,
        setAllStores: (state, payload) => state.allStores = [...payload],
        setMyStore: (state, payload) => state.myStore = {...payload},
        setIsLoadingMyStore: (state, payload) => state.isLoadingMyStore = payload,
        setIsAddingPriceFile: (state, payload) => state.isAddingPriceFile = payload,
    },
    actions: {
        loadAllStores: async({ commit }) => {
            commit('setIsLoadingAllStores', true)
            const { data } = await api.get('/stores')
            if (data) {
                commit('setAllStores', data)
            }
            commit('setIsLoadingAllStores', false)
        },
        loadMyStore: async({ commit }) => {
            commit('setIsLoadingMyStore', true)

            const { data } = await api.get('/stores/my')
            if (data) {
                commit('setMyStore', data)
            }
            commit('setIsLoadingMyStore', false)
        },
        createStore: async({ commit, dispatch }, obj) => {
            const { data } = await api.post('/stores', obj)
            if (data.status === 'success') {
                commit('setMyStore', data.data)
            }
            dispatch('notification/setSuccess', 'Магазин создан успешно', { root: true })
        },
        updateStore: async({ commit }, obj) => {
            const { data } = await api.put(`/stores/${obj.id}`, obj)
            if (data.status === 'success') {
                commit('setMyStore', data.data)
            }
        },
        addPriceFile: async({ commit }, obj) => {
            commit('setIsAddingPriceFile', true)
            const { data } = await api.post(`/stores/add-price-file/${obj.store_id}`, obj)
            if (data.status === 'success') {
                commit('setMyStore', data.data)
            }
            commit('setIsAddingPriceFile', true)
        }
    },
}
