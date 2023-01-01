import api from '../utils/api'

export default {
    namespaced: true,
    state:{
        isLoadingFromParser: false,
        previewFromParser:[],
        previewFromParserTotalCount: 0,
    },
    mutations:{
        setIsLoadingFromParser: (state, payload) => state.isLoadingFromParser = payload,
        setPreviewFromParser: (state, payload) => {
            state.previewFromParser = [...payload.items]
            state.previewFromParserTotalCount = payload.total
        },
    },
    actions:{
        loadProductsToDb: async ({ commit, dispatch }, object) => {
            const response = await api.post('/parser/save-products-with-skus', object)
            if (response.status === 'success') {
                dispatch('loadProductsFromParser', {
                    pageSize: 10,
                    page: 1
                })
            }
        },
        loadProductsFromParser: async ( { commit }, object = {}) => {
            commit('setIsLoadingFromParser', true)

            let params = {
                page: object.page,
                pageSize:object.pageSize,
                sortBy: object.sortDesc ? '-' + object.sortBy : object.sortBy,
            }

            const response = await api.get('/parser/load-products-with-skus', { params })
            if (response) {
                commit('setPreviewFromParser', response)
            }
            commit('setIsLoadingFromParser', false)
        }
    }
}
