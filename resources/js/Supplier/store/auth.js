import api from '../utils/api'

export default {
    namespaced: true,
    state: {
        isAuth:false,
        mailVerification: {
            isRequired: true,
            userEmail:'',
            after: ''
        },
        userName:'',
        userAvatar: '',
        userRole: '',
        isShowAuthModal: false
    },
    mutations: {
        SET_USER: (state, { userName, avatar,  role }) => {
            state.isAuth = true
            state.userName = userName
            state.userAvatar = avatar
            state.userRole = role
        },
        LOGOUT: state => {
            state.isAuth = false
            state.userName = ''
            state.userAvatar = ''
            state.userRole = ''
        },
        setEmailVerification: (state, { after, email }) => {
            state.mailVerification = {
                isRequired: true,
                after,
                userEmail: email
            }
        },
        verifyEmail: state => {
            state.mailVerification = {
                isRequired: false,
                after: '',
                userEmail: ''
            }
        },
        setIsShowAuthModal: (state, payload) => state.isShowAuthModal = payload
    },
    actions: {
        loginWithService: async({ commit }, service) => {
            const { url } = await api.get(`/login/${service}`)
            if (url) {
                window.location.href = url
                //window.open(url)
            }
        },
        loginViaSocialServices: async ({ commit }, obj) => {
            const { user_name, token, avatar} = obj
            localStorage.setItem('userData', JSON.stringify({userName: user_name, token, avatar, role: 'Client'}))
            commit('SET_USER', { userName: user_name, avatar, role: 'Client' })
            commit('setIsShowAuthModal', false)
        },
        login: async({ commit, dispatch }, object) => {
            const res = await api.post('/signin', object)
            if (res.status) {
                if (!res.isRequiredEmailVerification) {
                    const { message, user_name, token, avatar, role } = res
                    localStorage.setItem('userData', JSON.stringify({ userName: user_name,  token, avatar, role }))
                    commit('SET_USER', { userName: user_name, avatar, role })
                    commit('setIsShowAuthModal', false)
                } else {
                    commit('setEmailVerification', { email: res.email, after: 'login' })
                }

            } else {
                dispatch('notification/setSuccess', res.message, { root: true })
            }


        },
        register: async ({ commit, dispatch }, object) => {
            const res = await api.post('/signup', object)

            if (res.isRequiredEmailVerification) {
                commit('setEmailVerification', { email: res.email, after: 'register' })
            } else {
                dispatch('notification/setSuccess', 'Регистрация прошла успешно', { root: true })
            }
        },


        checkAuth: ({ commit }) => {
            const data = JSON.parse(localStorage.getItem('userData'))
            if (data && 'supplier' === data.role.toLowerCase()) {
                const { userName, role, avatar } = data
                commit('SET_USER', { userName, role, avatar })
            }
        },
        logout: async ({ commit }) => {
            const { message } = await api.post('/logout')
            localStorage.removeItem('userData')
            // dispatch('status/SET_NOTE', message, { root: true })
            commit('LOGOUT')
        },
        resendVerificationEmail: async({ state, dispatch }) => {
            const { message } = await api.post('/email/verification-notification', {email: state.mailVerification.userEmail})
            dispatch('notification/setSuccess', message, { root: true })
        }
    }
}
