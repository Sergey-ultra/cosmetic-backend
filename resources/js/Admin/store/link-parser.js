import api from '../utils/api'

export default {
    namespaced: true,
    state:{
        isParsing: false,
        message: ''
    },
    mutations: {
        setIsParsing: (state, payload) => state.isParsing = payload,
        setMessage: (state, payload) => state.message = payload,
    },
    actions: {
        parseLinks: async({ commit }, obj) => {
            commit('setIsParsing' , true)
            const { data } = await api.post("/parser/link/parse-links", obj)
            if (data) {
                commit('setMessage', data.message)
            }
            commit('setIsParsing' , false)
        },
    }
}
