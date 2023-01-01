import api from '../utils/api'

export default {
    namespaced: true,
    state:{
        isLoading: false,
        isLoadingPrice: false,
    },
    mutations: {
        setIsLoading: (state, payload) => state.isLoading = payload,
        setIsLoadingPrice: (state, payload) => state.isLoadingPrice = payload,
    },
    actions: {
        parsePriceByLinkIds: async({ commit }, ids) => {
            commit('setIsLoadingPrice' , true)
            const res = await api.post("/parser/price/parse-prices-by-link-ids", { ids })

            commit('setIsLoadingPrice' , false)
        },
        parsePricesFromActualPriceParsingTable: async({ commit }, storeId) => {
            commit('setIsLoading' , true)
            const res = await api.post("/parser/price/parse-prices-from-actual-price-parsing-table", { storeId})

            commit('setIsLoading' , false)
        },
    }
}
