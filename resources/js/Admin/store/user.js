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
            perPage: 50,
            sortBy:'',
            sortDesc: false
        },
        filterOptions: {
            role_id: { value: 'null'}
        },
        availableRoles: [],
        masterPassword: '',
        savingBotsStatus: '',
        myUser: null,
    },
    getters: {
        availableRoleNames: state => state.availableRoles.map(el => el.name),
        availableRoleIds: state => state.availableRoles.map(el => el.id),
    },
    mutations: {
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
                perPage: 50,
                sortBy: '',
                sortDesc: false
            }
        },
        setTableOptionsToDefault: state => state.tableOptions = {
            page: 1,
            perPage: 50,
            sortBy:'',
            sortDesc: false
        },
        setAllUsers: (state, payload) => state.allUsers = [...payload],
        setLoadedUser: (state, payload) => state.loadedUser = {...payload},
        setAvailableRoles: (state, payload) => state.availableRoles = [...payload],
        setMasterPassword: (state, payload) => state.masterPassword = payload,
        setSavingBotsStatus: (state, payload) => state.savingBotsStatus = payload,
        setMyUser: (state, payload) => state.myUser = {...payload},
    },
    actions: {
        loadMyUser: async({ commit }) => {
            const { data } = await api.get('/users/my');
            if (data) {
                commit('setMyUser', data);
            }
        },
        loadMasterPassword: async({ commit }) => {
            const { data } = await api.get('/users/master-password');
            if (data) {
                commit('setMasterPassword', data);
            }
        },
        saveUserBots: async({ commit }, payload)  => {
            const { data } = await api.post('/users/save-bots', payload);
            if (data) {
                commit('setSavingBotsStatus', data);
            }
        },
        loadAvailableRoles: async ({ commit }) => {
            const { data } = await api.get(`/users/show-available-roles`)
            if (data) {
                commit('setAvailableRoles', data);
            }
        },
        reloadUsers: ({commit, dispatch}) => {
            commit('setTableOptionsToDefault')
            dispatch('loadUsers')
        },
        loadAllUsers: async({ commit }) => {
            const { data } = await api.get(`/users`, { params: { per_page: -1 } })
            if (data) {
                commit('setAllUsers', data);
            }
        },
        loadUsers: async ({ commit, state }) => {
            commit('setIsLoading', true)

            const params = prepareQueryParams(state.tableOptions, state.filterOptions)

            const { data } = await api.get(`/users`, { params })
            if (data) {
                commit('setUsers', data);
            }
            commit('setIsLoading', false);
        },
        loadItem: async({ commit }, id) =>  {
            const { data } = await api.get(`/users/${id}`)
            if (data) {
                commit('setLoadedUser', data);
            }
        },
        createItem: async({ dispatch }, object) => {
            const { data } = await api.post('/users', object)
            if (data) {
                dispatch('reloadUsers');
            }
        },
        updateItem: async ({ dispatch }, object) => {
            const { data } = await api.put(`/users/${object.id}`, object)
            if (data) {
                dispatch('reloadUsers');
            }
        },
        deleteItem: async ({ dispatch }, id) => {
            //await api.delete(`/users/${id}`)

            dispatch('reloadUsers');
            dispatch('notification/setSuccess', 'Удаление временно невозможно', { root: true });
        }
    }
}
