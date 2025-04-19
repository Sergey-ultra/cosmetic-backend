import api from '../utils/api'
import prepareQueryParams from "../utils/prepareQueryParams";

export default {
    namespaced: true,
    state:{
        allIngredients: [],
        ingredients: [],
        isLoadingCurrentIngredient: false,
        loadedIngredient:{},
        tableOptions: {
            page: 1,
            perPage: 20,
            sortBy: '',
            sortDesc: false
        },
        total: 0,
        isLoading: false,
        filterOptions: {
            active_ingredients_group_name: { value: 'null' }
        },
        availableActiveIngredientsGroups:[]
    },
    getters: {
        //availableActiveIngredientsGroupNames: state => state.availableActiveIngredientsGroups.map(el => el.name)
    },
    mutations:{
        setIsLoading: (state, payload) => state.isLoading = payload,
        setIsLoadingCurrentIngredient: (state, payload) => state.isLoadingCurrentIngredient = payload,
        setTableOptions: (state, payload) => state.tableOptions = {...payload},
        setFilterOptions: (state, payload) => {
            state.filterOptions = {...payload}
            state.tableOptions = {
                page: 1,
                perPage: 20,
                sortBy: '',
                sortDesc: false
            }
        },
        setTableOptionsToDefault: state => state.tableOptions = {
            page: 1,
            perPage: 20,
            sortBy: '',
            sortDesc: false
        },
        setAllIngredients: (state, payload) => state.allIngredients = [...payload],
        setIngredients: (state, payload) => {
            state.ingredients = [...payload.data]
            state.total = payload.total
        },
        setLoadedIngredient: (state, payload) => state.loadedIngredient = {...payload},
        setAvailableActiveIngredientsGroups: (state, payload) => state.availableActiveIngredientsGroups = [...payload]
    },
    actions:{
        loadAllIngredients: async({ commit }) => {
            const res = await api.get('/ingredients',{ params: { per_page: -1 }})
            if (res) {
                commit('setAllIngredients', res)
            }
        },
        reloadIngredients: ({commit, dispatch}) => {
            commit('setTableOptionsToDefault')
            dispatch('loadIngredients')
        },
        loadIngredients: async({ commit, state }, object = {}) => {
            commit('setIsLoading', true)

            const params = prepareQueryParams(state.tableOptions, state.filterOptions)

            const res = await api.get(`/ingredients`, { params })
            if (res) {
                commit('setIngredients', res)
            }
            commit('setIsLoading', false)
        },
        loadAvailableActiveIngredientsGroups: async({ commit }) => {
            const { data } = await api.get(`ingredients/show-available-active-ingredients-groups`)
            if (data) {
                commit('setAvailableActiveIngredientsGroups', data)
            }

        },
        loadItem: async({ commit }, id) =>  {
            commit('setIsLoadingCurrentIngredient', true)

            const { data } = await api.get(`/ingredients/${id}`)
            if (data) {
                commit('setLoadedIngredient', data)
            }

            commit('setIsLoadingCurrentIngredient', false)
        },
        createItem: async({ dispatch }, object) => {
            const { data } = await api.post('/ingredients', object)
            if (data) {
                dispatch('reloadIngredients')
            }
        },
        updateItem: async ({ dispatch }, object) => {
            const { data } = await api.put(`/ingredients/${object.id}`, object)
            if (data) {
                dispatch('reloadIngredients')
            }
        },
        deleteItem: async ({ dispatch }, id) => {
            //await api.delete(`/ingredients/${id}`)

            dispatch('reloadIngredients')
            dispatch('notification/setSuccess', 'Удаление временно невозможно', { root: true })
        }
    }
}
