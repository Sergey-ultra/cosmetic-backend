import api from '../utils/api'

export default {
    namespaced: true,
    state:{
        isParsing: false,
        preview: null,
        isShowPreview: false,
        isOpenPreviewAfterParsing: false
    },
    mutations: {
        setIsParsing: (state, payload) => state.isParsing = payload,
        setPreview: (state, payload) => state.preview = { ...payload },
        setIsShowPreview: (state, payload) => state.isShowPreview = payload,
        setIsOpenPreviewAfterParsing: (state, payload) => state.isOpenPreviewAfterParsing = payload,
    },
    actions: {
        parseLinks: async({ commit, state }, obj) => {
            commit('setIsParsing' , true)
            const { data } = await api.post("/parser/link/parse-links", obj)
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
