export default {
    namespaced: true,
    state:{
        notes:[],
    },
    mutations: {
        setNote:(state, object) => state.notes.unshift(object),
        clearNote: state => state.notes.splice(state.notes.length - 1, 1),
        deleteNote: (state, index) => state.notes.splice(index, 1),
    },
    actions: {
        setSuccess: ({ commit }, message) => {
            commit('setNote', { status: 'success', message })
            setTimeout(() => {
                commit('clearNote')
            }, 10000)
        },
        setError: ({ commit }, message) => {
            commit('setNote', { status: 'error', message })
            setTimeout(() => {
                commit('clearNote')
            }, 10000)
        },
    },
}
