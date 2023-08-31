import api from '../utils/api'

export default {
    namespaced: true,
    state:{
        isParsing: false,
        preview: null,
        isShowPreview: false,
        isOpenPreviewAfterParsing: false,
        linkOptions: {
            categoryUrl: "",
            link: "",
            relatedLink: true,
            nextPage:"",
            relatedPageUrl: true,
            startPageNumber: 0,
            endPageNumber: null,
            paginationQuery: '',
        }
    },
    mutations: {
        setLinkOptions: (state, payload) => state.linkOptions = { ...payload },
        setIsParsing: (state, payload) => state.isParsing = payload,
        setPreview: (state, payload) => state.preview = { ...payload },
        setIsShowPreview: (state, payload) => state.isShowPreview = payload,
        setIsOpenPreviewAfterParsing: (state, payload) => state.isOpenPreviewAfterParsing = payload,
    },
    actions: {
        loadLinkOptions: async({ commit }, obj) => {
            const { data } = await api.get("/parser/review/link-option", { params: obj })
            if (data) {
                commit('setLinkOptions', data)

            } else {
                commit('setLinkOptions', {
                    categoryUrl: "",
                    link: "",
                    relatedLink: true,
                    nextPage:"",
                    relatedPageUrl: true,
                    startPageNumber: 0,
                    endPageNumber: null,
                    paginationQuery: '',
                })
            }
        },
        saveLinkOptions: async({ commit }, obj) => {
            const { data } = await api.post("/parser/review/link-option", obj)
        },
        parseLinks: async({ commit, state }, obj) => {
            commit('setIsParsing' , true)
            const { data } = await api.post("/parser/review/parse-links", obj)
            if (data) {
                commit('setPreview', data);
                if (state.isOpenPreviewAfterParsing) {
                    commit('setIsShowPreview', true)
                }
            }
            commit('setIsParsing' , false)
        },
    }
}
