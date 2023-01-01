import api from '../utils/api'

export default {
    namespaced: true,
    state:{
        productOptions: {
            imgTag: "",
            imgAttr: "",
            fileFields: [
                []
            ],
        }
    },
    getters:{

    },
    mutations: {
        setProductOptions: (state, payload) => state.productOptions= {...payload},
        setProductOptionsToDefault: state => {
            state.productOptions= {
                imgTag: "",
                imgAttr: "",
                fileFields: [
                    []
                ]
            }
        },
    },
    actions: {
        loadProductOptions: async({ commit }, storeId) => {
            const { data } = await api.get("/parser/product-option", { params: { store_id: storeId }})

            if (data) {
                commit('setProductOptions', data)
            }
        },
        saveProductOptions: async({ commit }, obj) => {
            const { status } = await api.post("/parser/product-option",  obj)
        }
    }
}
