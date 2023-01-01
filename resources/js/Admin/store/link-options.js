import api from '../utils/api'

export default {
    namespaced: true,
    state: {
        linkOptions: {
            categoryUrl: "",
            productLink: "",
            relatedLink: true,
            nextPage:"",
            relatedPageUrl: true
        }
    },
    mutations: {
        setLinkOptions: (state, payload) => state.linkOptions = { ...payload },
    },
    actions: {
        loadLinkOptions: async({ commit }, obj) => {
            const { data } = await api.get("/parser/link-option", { params: obj })
            if (data) {
                commit('setLinkOptions', data)

            } else {
                commit('setLinkOptions', {
                    categoryUrl: "",
                    productLink: "",
                    relatedLink: true,
                    nextPage:"",
                    relatedPageUrl: true
                })
            }
        },
        saveLinkOptions: async({ commit }, obj) => {
            const { status } = await api.post("/parser/link-option",obj)
        }
    }
}
