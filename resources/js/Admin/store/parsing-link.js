import api from '../utils/api'

export default {
    namespaced: true,
    state:{
        links: [],
        totalCount: 0,
        tableOptions: {
            pageSize: 20,
            page: 1,
            sortBy: '',
            sortDesc: false
        },
        availableStoresWithUnparsedLinks: [],
        storesWithLinksCount:[]
    },
    mutations: {
        setLinksWithPagination: (state, payload) => {
            state.links = [...payload.data]
            state.totalCount = payload.total
        },
        setLinksWithPaginationToDefault: state => {
            state.links = []
            state.totalCount = 0
        },
        setTableOptions: (state, payload) => state.tableOptions = {...payload},
        setTableOptionsToDefault: state => state.tableOptions = {
            page: 1,
            pageSize: 20,
            sortBy: '',
            sortDesc: false,

        },
        setAvailableStoresWithUnparsedLinks: (state, payload) => state.availableStoresWithUnparsedLinks = [...payload],
        setStoresWithLinksCount: (state, payload) => state.storesWithLinksCount = [...payload],
        updateCountBeforeEnd: (state, payload) => {
            const index = state.availableStoresWithUnparsedLinks.findIndex(el => el.id === payload.storeId);
            state.availableStoresWithUnparsedLinks[index].countBeforeEnd = payload.count
        }
    },
    actions: {
        loadLinksWithPagination: async ({ commit, state }, obj) => {

            let params = {
                store_id: obj.store_id,
                page: state.tableOptions.page,
                pageSize: state.tableOptions.pageSize,
            }

            if (state.tableOptions.sortBy) {
                let sortBy = state.tableOptions.sortBy
                if (state.tableOptions.sortDesc) {
                    sortBy = '-' + sortBy
                }
                params.sortBy = sortBy
            }

            if (obj.forPrice) {
                params.forPrice = true
            }

            const { data } = await api.get("/parser/parsed-links", { params })

            if (data) {
                commit('setLinksWithPagination', data)
            }
        },
        loadStoresList: async({ commit }) => {
            const { data } = await api.get("/parser/parsed-links/stores-with-unparsed-links-count")
            if (data) {
                commit('setAvailableStoresWithUnparsedLinks', data)
            }
        },
        loadStoresWithLinksCount: async ({ commit }) => {
            const { data } = await api.get("/parser/parsed-links/stores-with-links-count")
            if (data) {
                commit('setStoresWithLinksCount', data)
            }
        },
        deleteBodyFromParsingLink: async ({ dispatch }, id) => {
            await api.delete(`/parser/parsed-links/delete-body-from-parsing-link/${id}`);
            dispatch('notification/setSuccess', 'Тело данной ссылки успешно удалено', { root: true })
        }
    }
}
