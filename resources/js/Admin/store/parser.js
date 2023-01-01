import api from '../utils/api'

export default {
    namespaced: true,
    state:{
        messageAfterLoadPricesToDb: 'Выберите магазин и нажмите кнопку',
        messageAfterLoadStoresToDb: '',
        isLoadingBrand: false,
        isLoadingCategory: false,
        isLoadingCountry: false,
        isLoadingImage: false,
        isLoadingLink: false,
        isLoadingProduct: false,
        isLoadingSku: false,
        isLoadingPriceHistory: false,
        isLoadingCurrentPrice: false,
        isLoadingStore: false,
        isLoadingIngredient: false,
        isLoadingIngredientProduct: false,
    },
    mutations:{
        setIsLoadingBrand: (state, payload) => state.isLoadingBrand = payload,
        setIsLoadingCategory: (state, payload) => state.isLoadingCategory = payload,
        setIsLoadingCountry: (state, payload) => state.isLoadingCountry = payload,
        setIsLoadingImage: (state, payload) => state.isLoadingImage = payload,
        setIsLoadingLink: (state, payload) => state.isLoadingLink = payload,
        setIsLoadingProduct: (state, payload) => state.isLoadingProduct = payload,
        setIsLoadingSku: (state, payload) => state.isLoadingSku = payload,
        setIsLoadingPriceHistory: (state, payload) => state.isLoadingPriceHistory = payload,
        setIsLoadingCurrentPrice: (state, payload) => state.isLoadingCurrentPrice = payload,
        setIsLoadingStore: (state, payload) => state.isLoadingStore = payload,
        setIsLoadingIngredient: (state, payload) => state.isLoadingIngredient = payload,
        setIsLoadingIngredientProduct: (state, payload) => state.isLoadingIngredientProduct = payload,

        setMessageAfterLoadPricesToDb: (state, payload) => state.messageAfterLoadPricesToDb = payload,
        setMessageAfterLoadStoresToDb: (state, payload) => state.messageAfterLoadStoresToDb = payload,
    },
    actions:{
        loadBrand: async ({ commit }) => {
            commit('setIsLoadingBrand', true)
            const res = await api.get('/parser/save-brand')
            commit('setIsLoadingBrand', false)
        },
        loadCategory: async ({ commit }) => {
            commit('setIsLoadingCategory', true)
            const res = await api.get('/parser/save-category')
            commit('setIsLoadingCategory', false)
        },
        loadCountry: async ({ commit }) => {
            commit('setIsLoadingCountry', true)
            const res = await api.get('/parser/save-country')
            commit('setIsLoadingCountry', false)
        },

        loadLink: async ({ commit }) => {
            commit('setIsLoadingLink', true)
            const res = await api.get('/parser/save-link')
            commit('setIsLoadingLink', false)
        },
        loadProduct: async ({ commit }) => {
            commit('setIsLoadingProduct', true)
            const res = await api.get('/parser/save-product')
            commit('setIsLoadingProduct', false)
        },
        loadSku: async ({ commit, state }) => {
            commit('setIsLoadingSku', true)
            const res = await api.get('/parser/save-sku')
            commit('setIsLoadingSku', false)
        },
        loadPriceHistory: async ({ commit }, object) => {
            commit('setIsLoadingPriceHistory', true)

            const res = await api.get('/parser/save-price-history')
            if (res.status) {
                commit('setMessageAfterLoadPricesToDb', res.message)
            }
            commit('setIsLoadingPriceHistory', false)
        },
        loadStore: async ({ commit }) => {
            commit('setIsLoadingStore', true)

            const res = await api.get('/parser/save-store')
            if (res.status) {
                commit('setMessageAfterLoadStoresToDb', res.message)
            }

            commit('setIsLoadingStore', false)
        },
        loadIngredient: async ({commit}) => {
            commit('setIsLoadingIngredient', true)

            const res = await api.get('/parser/save-ingredient')
            if (res.status) {
                commit('setMessageAfterLoadStoresToDb', res.message)
            }

            commit('setIsLoadingIngredient', false)
        },
        loadIngredientProduct: async ({commit}) => {
            commit('setIsLoadingIngredientProduct', true)

            const res = await api.get('/parser/save-ingredient-product')
            if (res.status) {
                commit('setMessageAfterLoadStoresToDb', res.message)
            }

            commit('setIsLoadingIngredientProduct', false)
        },
        loadCurrentPrice: async ({commit}) => {
            commit('setIsLoadingCurrentPrice', true)

            const res = await api.get('/parser/save-current-price')
            if (res.status) {
                commit('setMessageAfterLoadStoresToDb', res.message)
            }

            commit('setIsLoadingCurrentPrice', false)
        },

    }
}
