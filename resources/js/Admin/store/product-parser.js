import api from '../utils/api'

export default {
    namespaced: true,
    state:{
        isParsingProduct: false,
        preview:[],
        isReloadLinks: false,
        isShowPreview: false,
        isOpenPreviewAfterParsing: false,
    },
    getters:{

    },
    mutations: {
        setIsParsingProduct: (state, payload) => state.isParsingProduct = payload,
        setPreview: (state, payload) => state.preview = [...payload],
        setIsReloadLinks: (state, payload) => state.isReloadLinks = payload,
        setIsShowPreview: (state, payload) => state.isShowPreview = payload,
        setIsOpenPreviewAfterParsing: (state, payload) => state.isOpenPreviewAfterParsing = payload,
    },
    actions: {
        parseProductByLinkIds: async({ commit, dispatch, state }, obj) => {
            commit('setIsParsingProduct', true)

            const { data } = await api.post("/parser/product/parse-product-by-link-ids", obj)

            if (data) {
                if (data.message === "success") {
                    commit('setIsReloadLinks', true)
                    if (data.message2) {
                        dispatch('notification/setSuccess', data.message2, { root: true })
                    }
                    commit('setPreview', data.data)
                } else {
                    commit('setPreview', [data])
                }
                if (state.isOpenPreviewAfterParsing) {
                    commit('setIsShowPreview', true)
                }
            }

            commit('setIsParsingProduct', false)
        },
        compressAllUncompressedImages: async({ commit }) => {
            const res = await api.get("/parser/product/compress-all-uncompressed-images")
        },
    }
}
