import api from '../utils/api'

export default {
    namespaced: true,
    state: {
        adminMessages: [],
        lastMessageId: null,
        messages: [],
        isLoadingChat: false,
        dialogUser: '',
        dialogAvatar: '',
        dialogUserId: '',
    },
    mutations: {
        setAdminMessages: (state, payload) => state.adminMessages = [...payload],
        setLastMessageId: (state, payload) => state.lastMessageId = payload,
        setIsLoadingChat: (state, payload) => state.isLoadingChat = payload,
        setMessages: (state, payload) => state.messages = [...payload],
        setDialogUser: (state, payload) => state.dialogUser = payload,
        setDialogAvatar: (state, payload) => state.dialogAvatar = payload,
        setDialogUserId: (state, payload) => state.dialogUserId = payload,
        addMessage: (state, payload) => state.messages.push(payload),
    },
    actions: {
        loadPriceOptions: async({ commit }, storeId) => {
            const { data } = await api.get("/my-messages");

            if (data && Array.isArray(data)) {
                commit('setAdminMessages', data)
            }
        },
        loadChatByMessageId: async({ commit }, id) => {
            commit('setIsLoadingChat', true);

            const { data } = await api.get(`/chats/${id}`);
            if (data && Array.isArray(data.messages)) {
                commit('setMessages', data.messages);
                commit('setDialogUser', data.dialog_user);
                commit('setDialogAvatar', data.dialog_avatar);
                commit('setDialogUserId', data.dialog_user_id);
            }
            commit('setIsLoadingChat', false);
        },
        sendMessage: async({ commit, dispatch }, payload) => {
            const { data } = await api.post('/messages', payload);

            if (data.id) {
                commit('addMessage', data);
                dispatch('notification/setSuccess', 'Сообщение успешно отправлено', { root: true })
            }
        },
    }
}
