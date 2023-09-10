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
        },
        isParsingLinks: false,
        links: [],
        totalCount: 0,
        tableOptions: {
            pageSize: 20,
            page: 1,
            sortBy: '',
            sortDesc: false
        },
        isReloadLinks: false,
        currentReviewData: {
            body: [],
            title: '',
        },
        isLoadingReviewData: false,
    },
    mutations: {
        setLinkOptions: (state, payload) => state.linkOptions = { ...payload },
        setIsParsing: (state, payload) => state.isParsing = payload,
        setIsParsingLinks: (state, payload) => state.isParsingLinks = payload,
        setPreview: (state, payload) => state.preview = { ...payload },
        setIsShowPreview: (state, payload) => state.isShowPreview = payload,
        setIsReloadLinks: (state, payload) => state.isReloadLinks = payload,
        setIsOpenPreviewAfterParsing: (state, payload) => state.isOpenPreviewAfterParsing = payload,
        setLinksWithPagination: (state, payload) => {
            state.links = [...payload.data]
            state.totalCount = payload.total
        },
        setParsedLinksWithPagination: (state, payload) => {
            state.links = [...payload.data]
            state.totalCount = payload.meta.total
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
        setCurrentReviewData: (state, payload) => state.currentReviewData = {...payload},
        setIsLoadingReviewData: (state, payload) => state.isLoadingReviewData = payload,
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
        loadLinksWithPagination: async ({commit, state}) => {
            let params = {
                page: state.tableOptions.page,
                per_page: state.tableOptions.pageSize,
            }

            if (state.tableOptions.sortBy) {
                let sortBy = state.tableOptions.sortBy
                if (state.tableOptions.sortDesc) {
                    sortBy = '-' + sortBy
                }
                params.sortBy = sortBy
            }

            const { data } = await api.get("/parser/review/links", { params })

            if (data) {
                commit('setLinksWithPagination', data)
            }
        },
        loadParsedLinksWithPagination: async({ commit, state }) => {
            let params = {
                page: state.tableOptions.page,
                per_page: state.tableOptions.pageSize,
            }

            if (state.tableOptions.sortBy) {
                let sortBy = state.tableOptions.sortBy
                if (state.tableOptions.sortDesc) {
                    sortBy = '-' + sortBy
                }
                params.sortBy = sortBy
            }

            const response = await api.get("/parser/review/parsed-links", { params })

            if (response) {
                commit('setParsedLinksWithPagination', response)
            }
        },
        loadReviewData: async({ commit }, id) => {
            commit('setIsLoadingReviewData', true);
            const { data } = await api.get(`/parser/review/parsed-links/${id}`);
            if (data) {
               commit('setCurrentReviewData', data);
            }
            commit('setIsLoadingReviewData', false);
        },
        setPublished: async({ commit }, id) => {
            const { data } = await api.post(`/parser/review/parsed-links/set-published/${id}`);
        },
        setArchived: async({ commit }, id) => {
            const { data } = await api.post(`/parser/review/parsed-links/set-archived/${id}`);
        },
        deleteBodyFromParsingLink: async ({ dispatch }, id) => {
            await api.delete(`/parser/review/parsed-links/delete-body-from-parsing-link/${id}`);
            dispatch('notification/setSuccess', 'Тело данной ссылки успешно удалено', { root: true })
        },
        parseByLinkIds: async({ commit, dispatch, state }, obj) => {
            commit('setIsParsingLinks', true)

            const { data } = await api.post("/parser/review/parse-by-link-ids", obj)

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
                // if (state.isOpenPreviewAfterParsing) {
                //     commit('setIsShowPreview', true)
                // }
            }

            commit('setIsParsingLinks', false)
        },
    },
}
