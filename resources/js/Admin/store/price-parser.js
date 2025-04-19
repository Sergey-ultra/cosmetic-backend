import api from '../utils/api'

export default {
    namespaced: true,
    state:{
        isLoading: false,
        isLoadingPrice: false,
        maxLinkCountPerStore: 0,
        minHourLinkCount: null,
        maxHourLinkCount: null,
    },
    mutations: {
        setIsLoading: (state, payload) => state.isLoading = payload,
        setIsLoadingPrice: (state, payload) => state.isLoadingPrice = payload,
        setMaxLinkCountPerStore: (state, payload) => state.maxLinkCountPerStore = payload,
        setMinHourLinkCount: (state, payload) => state.minHourLinkCount = payload,
        setMaxHourLinkCount: (state, payload) => state.maxHourLinkCount = payload,
    },
    actions: {
        parsePriceByLinkIds: async({ commit }, ids) => {
            commit('setIsLoadingPrice' , true)
            const res = await api.post("/parser/price/parse-prices-by-link-ids", { ids })
            commit('setIsLoadingPrice' , false)
        },
        parsePricesFromActualPriceParsingTable: async({ commit }, storeId) => {
            commit('setIsLoading' , true)
            const res = await api.post("/parser/price/parse-prices-from-actual-price-parsing-table", { storeId});
            commit('setIsLoading' , false)
        },
        getMaxLinkCountPerStore: async({ commit }) => {
            const { data } = await api.get("/parser/price/max-link-count-per-store");
            if (typeof data === 'number') {
                commit('setMaxLinkCountPerStore', data)
            }
        },
        getMinHourCount: async({ commit }) => {
            const { data } = await api.get('/parser/price/get-min-hour-count');
            if (typeof data === 'number') {
                commit('setMinHourLinkCount', data);
            }
        },
        getMaxHourCount: async({ commit }) => {
            const { data } = await api.get('/parser/price/get-max-hour-count');
            if (typeof data === 'number') {
                commit('setMaxHourLinkCount', data);
            }
        },
        setMinHourCount: async ({ commit }, minHourCount) => {
            const { data } = await api.post("/parser/price/set-min-hour-count", { hour_count: minHourCount});
            if (data.status === 'success') {
                commit('setMinHourLinkCount', minHourCount);
            }
        },
        setMaxHourCount: async ({ commit }, maxHourCount) => {
            const { data } = await api.post("/parser/price/set-max-hour-count", { hour_count: maxHourCount});
            if (data.status === 'success') {
                commit('setMaxHourLinkCount', maxHourCount);
            }
        },
    }
}
