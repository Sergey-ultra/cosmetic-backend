import api from '../utils/api'
import {defineStore} from "pinia";
import {useNotificationStore} from "./notification";
const notificationStore = useNotificationStore();

export const useAuthStore = defineStore({
    id: 'auth',
    state: () => ({
        isAuth:false,
        mailVerification: {
            isRequired: true,
            userEmail:'',
            after: ''
        },
        userName:'',
        userAvatar: '',
        userRole:'',
        isShowAuthModal: false
    }),
    actions: {
        SET_USER({ userName, avatar,  role }) {
            this.isAuth = true;
            this.userName = userName;
            this.userAvatar = avatar;
            this.userRole = role;
        },
        LOGOUT()  {
            this.isAuth = false;
            this.userName = '';
            this.userAvatar = '';
            this.userRole = '';
        },
        setEmailVerification({ after, email }) {
            this.mailVerification = {
                isRequired: true,
                after,
                userEmail: email
            }
        },
        verifyEmail() {
            this.mailVerification = {
                isRequired: false,
                after: '',
                userEmail: ''
            }
        },
        setIsShowAuthModal(payload) {
            this.isShowAuthModal = payload
        },
        async loginWithService(service) {
            const { url } = await api.get(`/login/${service}`)
            if (url) {
                window.location.href = url
            }
        },
        async loginViaSocialServices(obj) {
            const { user_name, token, avatar} = obj
            localStorage.setItem('userData', JSON.stringify({userName: user_name, token, avatar, role: 'Client'}))
            this.SET_USER({ userName: user_name, avatar, role: 'Client' })
            this.setIsShowAuthModal(false)
        },
        async login(object) {
            const res = await api.post('/login', object);
            if (res.status) {
                if (!res.isRequiredEmailVerification) {
                    const { message, user_name, token, avatar, role } = res;
                    localStorage.setItem('userData', JSON.stringify({ userName: user_name,  token, avatar, role }));
                    this.SET_USER({ userName: user_name, avatar, role });
                    this.setIsShowAuthModal(false);
                } else {
                    this.setEmailVerification({ email: res.email, after: 'login' });
                }

            } else {
                notificationStore.setSuccess(res.message);
            }
        },
        async register(object) {
            const res = await api.post('/register', object);

            if (res.isRequiredEmailVerification) {
                this.setEmailVerification({ email: res.email, after: 'register' });
            }
        },
        async checkAuth() {
            const data = await JSON.parse(localStorage.getItem('userData'));
            if (data) {
                const { userName, role, avatar } = data;
                this.SET_USER({ userName, role, avatar });
            }
        },
        async logout() {
            const { message } = await api.post('/logout');
            localStorage.removeItem('userData');
            this.LOGOUT();
        },
        async resendVerificationEmail() {
            const { message } = await api.post('/email/verification-notification', {email: state.mailVerification.userEmail});
            notificationStore.setSuccess(message);
        }
    }
});
