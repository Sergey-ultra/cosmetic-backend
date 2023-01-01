import api from '../utils/api'

export default {
    namespaced: true,
    state:{
        isAuth:false,
        userName: '',
        userRole:'',
        userAvatar: '',
    },
    mutations: {
        SET_USER: (state, { userName, role, avatar }) => {
            state.isAuth = true
            state.userName = userName
            state.userRole = role
            state.userAvatar = avatar
        },
        LOGOUT: state => {
            state.isAuth = false
            state.userName = ''
            state.userRole = ''
            state.userAvatar = ''
        }
    },
    actions: {
        login: async({ commit, dispatch }, object) => {
            object.asAdmin = true
            const res = await api.post('/login', object)

            if (res.status && !res.isRequiredEmailVerification && ['admin', 'moderator'].includes(res.role.toLowerCase())) {
                dispatch('notification/setSuccess', res.message, { root: true })
                const { message, user_name, token, avatar, role } = res
                localStorage.setItem('userData', JSON.stringify({ userName: user_name,  token, avatar, role }))
                commit('SET_USER', { userName: user_name, avatar, role })

            } else {
                dispatch('notification/setError', res.message, { root: true })
            }
        },
        checkAuth: ({ commit }) => {
            const data = JSON.parse(localStorage.getItem('userData'))
            if (data && ['admin', 'moderator'].includes(data.role.toLowerCase())) {
                const { userName, role, avatar } = data
                commit('SET_USER', { userName, role, avatar })
            }
        },
        logout: async ({ commit, dispatch }) => {
            const res = await api.post('/logout')
            if (res.status) {
                localStorage.removeItem('userData')
                dispatch('notification/setSuccess', res.message, { root: true })
                commit('LOGOUT')
            } else {
                dispatch('notification/setError', res.message, { root: true })
            }
        },
    }
}
