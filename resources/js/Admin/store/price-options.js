import api from '../utils/api'

export default {
    namespaced: true,
    state: {
        priceTag:''
    },
    mutations: {
        setPriceOptions: (state, payload) => state.priceTag = payload,
    },
    actions: {
        loadPriceOptions: async({ commit }, storeId) => {
            const { data } = await api.get("/parser/price-option", { params: { store_id: storeId }})

            if (data) {
                commit('setPriceOptions', data.priceTag)
            } else {
                commit('setPriceOptions', '')
            }
        },
        savePriceOptions: async({ commit }, obj) => {
            const { status } = await api.post("/parser/price-option",  obj)
        }
    }
}
