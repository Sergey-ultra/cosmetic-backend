import api from '../utils/api'

export default {
    namespaced: true,
    state:{
        isLoading: false,
        message: ''
    },
    mutations: {
        setIsLoading: (state, payload) => state.isLoading = payload,
        setMessage: (state, payload) => state.message = payload,
    },
    actions: {
        parseLinks: async({ commit }, obj) => {
            commit('setIsLoading' , true)
            const { data } = await api.post("/parser/link/parse-links", obj)
            if (data) {
                commit('setMessage', data.message)
            }
            commit('setIsLoading' , false)
        },
    }
}
