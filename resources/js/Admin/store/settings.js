import api from '../utils/api'


export default {
    namespaced: true,
    state: {
        isSetting: false,
        isRequiredEmailVerification: null
    },
    mutations:{
        setIsRequiredEmailVerification: (state, payload) => state.isRequiredEmailVerification = payload,
        setIsSetting: (state, payload) => state.isSetting = payload,
    },
    actions:{
        async setIsRequiredEmailVerification({ commit }, value) {
            commit('setIsSetting', true);
            await api.post('/settings/set-is-required-email-verification', {
                is_required_email_verification: value
            });
            commit('setIsRequiredEmailVerification', value)
            commit('setIsSetting', false);
        },
        async loadIsRequiredEmailVerification({ commit }) {
            const { data } = await api.get('/settings/get-is-required-email-verification')
            commit('setIsRequiredEmailVerification', Boolean(data))
        }
    }
}
