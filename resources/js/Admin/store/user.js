import api from '../utils/api'
import prepareQueryParams from '../utils/prepareQueryParams'

export default {
    namespaced: true,
    state:{
        total:0,
        isLoading: false,
        users:[],
        allUsers:[],
        loadedUser:{},
        tableOptions: {
            page: 1,
            perPage: 10,
            sortBy:'',
            sortDesc: false
        },
        filterOptions: {
            role: { value: 'null'}
        },
        availableRoles: []
    },
    getters: {
        availableRoleNames: state => state.availableRoles.map(el => el.name)
    },
    mutations:{
        setIsLoading: (state, data) => state.isLoading = data,
        setUsers: (state, payload) => {
            state.users = [...payload.data]
            state.total = payload.total
        },
        setTableOptions: (state, payload) => state.tableOptions = {...payload},
        setFilterOptions: (state, payload) => {
            state.filterOptions = {...payload}
            state.tableOptions = {
                page: 1,
                perPage: 10,
                sortBy: '',
                sortDesc: false
            }
        },
        setTableOptionsToDefault: state => state.tableOptions = {
            page: 1,
            perPage: 10,
            sortBy:'',
            sortDesc: false
        },
        setAllUsers: (state, payload) => state.allUsers = [...payload],
        setLoadedUser: (state, payload) => state.loadedUser = {...payload},
        setAvailableRoles: (state, payload) => state.availableRoles = [...payload]
    },
    actions:{
        loadAvailableRoles: async ({ commit }) => {
            const { data } = await api.get(`/users/show-available-roles`)
            if (data) {
                commit('setAvailableRoles', data)
            }
        },
        reloadUsers: ({commit, dispatch}) => {
            commit('setTableOptionsToDefault')
            dispatch('loadUsers')
        },
        loadUsers: async ({ commit, state }) => {
            commit('setIsLoading', true)

            const params = prepareQueryParams(state.tableOptions, state.filterOptions)

            const { data } = await api.get(`/users`, { params })
            if (data) {
                commit('setUsers', data)
            }
            commit('setIsLoading', false)
        },
        loadItem: async({ commit }, id) =>  {
            const { data } = await api.get(`/users/${id}`)
            if (data) {
                commit('setLoadedUser', data)
            }
        },
        createItem: async({ dispatch }, object) => {
            const { data } = await api.post('/users', object)
            if (data) {
                dispatch('reloadUsers')
            }
        },
        updateItem: async ({ dispatch }, object) => {
            const { data } = await api.put(`/users/${object.id}`, object)
            if (data) {
                dispatch('reloadUsers')
            }
        },
        deleteItem: async ({ dispatch }, id) => {
            //await api.delete(`/users/${id}`)

            dispatch('reloadUsers')
            dispatch('notification/setSuccess', 'Удаление временно невозможно', { root: true })
        }
    }
}
