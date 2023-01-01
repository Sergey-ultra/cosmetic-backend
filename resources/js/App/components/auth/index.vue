<template>
    <!--    <transition name="pop"  mode="out-in">-->
    <modal

            :width="width"
            v-model:isShowModal="isShowAuthModalLocal"
    >
        <template v-slot:header>
            <h3>{{ isLoginShow ? 'Войдите с помощью' : 'Регистрация'}}</h3>
        </template>

        <div v-if="mailVerification.isRequired && mailVerification.after === 'register'">Необходимо подтвердить e-mail</div>

        <div v-else-if="mailVerification.isRequired && mailVerification.after === 'login'" class="row">
            На ваш email {{ mailVerification.userEmail }} выслано подтверждение аккаунта
            <btn class="btn" :height="35" :color="'grey'" @click="authStore.resendVerificationEmail">Выслать повторно</btn>
            <btn class="btn" :height="35" @click="authStore.setIsShowAuthModal(false)">Понятно</btn>
        </div>

        <div v-else>
            <div v-if="isLoginShow">
                <div class="auth">
                    <div class="auth__wrap">
                        <div class="auth__wrap-el auth__with-g" @click="authStore.loginWithService('google')">
                            <div class="icon">
                                <img src="/storage/icons/signin-with-g.svg" alt="signin-with-g.svg">
                            </div>
                        </div>

<!--                        <div class="auth__wrap-el auth__with-g">-->
<!--                            <div class="icon__instagram">-->
<!--                                <svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 48 48" width="34px" height="34px"><radialGradient id="yOrnnhliCrdS2gy~4tD8ma" cx="19.38" cy="42.035" r="44.899" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#fd5"/><stop offset=".328" stop-color="#ff543f"/><stop offset=".348" stop-color="#fc5245"/><stop offset=".504" stop-color="#e64771"/><stop offset=".643" stop-color="#d53e91"/><stop offset=".761" stop-color="#cc39a4"/><stop offset=".841" stop-color="#c837ab"/></radialGradient><path fill="url(#yOrnnhliCrdS2gy~4tD8ma)" d="M34.017,41.99l-20,0.019c-4.4,0.004-8.003-3.592-8.008-7.992l-0.019-20	c-0.004-4.4,3.592-8.003,7.992-8.008l20-0.019c4.4-0.004,8.003,3.592,8.008,7.992l0.019,20	C42.014,38.383,38.417,41.986,34.017,41.99z"/><radialGradient id="yOrnnhliCrdS2gy~4tD8mb" cx="11.786" cy="5.54" r="29.813" gradientTransform="matrix(1 0 0 .6663 0 1.849)" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#4168c9"/><stop offset=".999" stop-color="#4168c9" stop-opacity="0"/></radialGradient><path fill="url(#yOrnnhliCrdS2gy~4tD8mb)" d="M34.017,41.99l-20,0.019c-4.4,0.004-8.003-3.592-8.008-7.992l-0.019-20	c-0.004-4.4,3.592-8.003,7.992-8.008l20-0.019c4.4-0.004,8.003,3.592,8.008,7.992l0.019,20	C42.014,38.383,38.417,41.986,34.017,41.99z"/><path fill="#fff" d="M24,31c-3.859,0-7-3.14-7-7s3.141-7,7-7s7,3.14,7,7S27.859,31,24,31z M24,19c-2.757,0-5,2.243-5,5	s2.243,5,5,5s5-2.243,5-5S26.757,19,24,19z"/><circle cx="31.5" cy="16.5" r="1.5" fill="#fff"/><path fill="#fff" d="M30,37H18c-3.859,0-7-3.14-7-7V18c0-3.86,3.141-7,7-7h12c3.859,0,7,3.14,7,7v12	C37,33.86,33.859,37,30,37z M18,13c-2.757,0-5,2.243-5,5v12c0,2.757,2.243,5,5,5h12c2.757,0,5-2.243,5-5V18c0-2.757-2.243-5-5-5H18z"/></svg>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="auth__wrap-el auth__with-fb" @click="authStore.loginWithService('facebook')">-->
<!--                            <div class="icon">-->
<!--                                <svg xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" data-name="Layer 21" image-rendering="optimizeQuality" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" viewBox="0 0 32 32"><path fill="#fefefe" d="M17.09 32H1.77C.79 32 0 31.21 0 30.23V1.77C0 .79.79 0 1.77 0h28.46C31.21 0 32 .79 32 1.77v28.46c0 .98-.79 1.77-1.77 1.77h-8.15V19.61h4.16l.62-4.83h-4.78v-3.09c0-1.39.39-2.35 2.39-2.35h2.56V5.02c-.44-.06-1.96-.19-3.73-.19-3.68 0-6.21 2.25-6.21 6.39v3.56h-4.17v4.83h4.17V32z"/></svg>-->
<!--                            </div>-->
<!--                        </div>-->

                        <div class="auth__wrap-el auth__with-vk" @click="authStore.loginWithService('vkontakte')">
                            <div class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill-rule="evenodd" clip-rule="evenodd" data-name="Layer 21" image-rendering="optimizeQuality" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" viewBox="0 0 41 23"><path fill="#fff" d="M39.39.89h-6.65c-.55 0-1.06.3-1.32.79-.26.49-2.66 4.89-3.51 6.53-2.29 4.43-3.78 3.04-3.78.99V2.11c0-1.23-1-2.23-2.23-2.23h-5c-.1-.01-.2-.01-.3-.01-1.28 0-2.49.61-3.24 1.65-.76 1.04 2.54-.41 2.54 3.01 0 .85.04 3.29.08 5.34v.04c0 .81-.66 1.47-1.47 1.47-.42 0-.83-.18-1.1-.49-2.04-2.84-3.75-5.94-5.05-9.19-.23-.49-.73-.81-1.28-.81H1.03C.46.89 0 1.35 0 1.92c0 .12.02.24.06.35C1.9 7.31 9.81 23.13 18.83 23.13h3.8c.83 0 1.5-.67 1.5-1.5v-2.29c0-.82.66-1.48 1.48-1.48.37 0 .73.15 1.01.4l4.54 4.27c.41.39.95.6 1.51.6h5.97c2.88 0 2.88-2 1.31-3.54-1.1-1.09-5.09-5.29-5.09-5.29-.37-.39-.58-.9-.58-1.43 0-.45.15-.89.42-1.25 1.29-1.7 3.4-4.47 4.29-5.66 1.22-1.63 3.43-5.07.4-5.07z"/></svg>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="section-hr">
                    <div class="section-hr__line"></div>
                    <div class="section-hr__text"> или </div>
                    <div class="section-hr__line"></div>

                </div>
                <form id="log-form" @submit.prevent="signIn">
                    <div class="form-group">
                        <input
                                v-model.trim="loginForm.email"
                                type="text"
                                name="email"
                                autofocus
                                class="form-control"
                                placeholder="E-mail"
                        >

                        <div class="invalid-feedback" v-for="error of v$.loginForm.email.$errors" :key="error.$uid">
                            {{ error.$message }}
                        </div>

                    </div>

                    <div class="form-group">
                        <input v-model.trim="loginForm.password"
                               type="password"
                               name="password"
                               placeholder="Пароль"
                               class="form-control"
                        >
                        <div class="invalid-feedback" v-for="error of v$.loginForm.password.$errors" :key="error.$uid">
                            {{ error.$message }}
                        </div>
                        <a class="form-forget">Забыли пароль?</a>
                    </div>


                    <button class="btn login" type="submit">Войти</button>
                    <div class="btn reg" @click="showRegistration"><span>Регистрация</span></div>
                </form>
            </div>


            <register
                    v-else
                    v-model:isLoginShow="isLoginShow"
            />
        </div>

    </modal>
    <!--    </transition>-->

</template>

<script setup>
    import useVuelidate from '@vuelidate/core'
    import {required, email, minLength, helpers} from '@vuelidate/validators'
    import register from "./register";
    import modal from '../modal'
    import btn from "../btn";
    import {computed, onMounted, reactive, ref, watch} from "vue";
    import {useAuthStore} from "../../store/auth";
    import {storeToRefs} from "pinia";

    const authStore = useAuthStore();
    const { isShowAuthModal, mailVerification } = storeToRefs(authStore);

    const loginForm = ref({
        password: '',
        email: '',
    });

    let isLoginShow = ref(true);

    let rules = {
        loginForm: {
            email: {
                required:  helpers.withMessage('Поле должно быть заполнено', required),
                email: helpers.withMessage('Не правильно введен email', email)
            },
            password: {
                required:  helpers.withMessage('Поле должно быть заполнено', required),
                minLength: helpers.withMessage('Должно быть не меньше 8 символов', minLength(8))
            }
        },
    };

    const v$ = useVuelidate(rules, {loginForm});


    let isShowAuthModalLocal = computed({
        get() {
            return isShowAuthModal.value;
        },
        set(value) {
            authStore.setIsShowAuthModal(value);
        }
    });

    let width = computed(() => {
            const width = document.documentElement.clientWidth

            if (width < 1470 && width > 1200) {
                return 30;
            }
            if (width < 1200 && width > 900) {
                return 35;
            }
            if (width < 900 && width > 700) {
                return 50;
            }
            if (width < 700 && width > 500) {
                return 70;
            }

            if (width < 500) {
                return 95;
            }

            return 25;
        }
    );

    const showRegistration = () => isLoginShow.value = false;

    const signIn =  async() => {
        const validated = await v$.value.loginForm.$validate();

        if (validated) {
            await authStore.login(loginForm.value);
            loginForm.value = {
                email: '',
                password: '',
            };

            v$.value.$reset();
        }
    };


    watch(isShowAuthModal, value => {
        if (!value) {
            isLoginShow.value = true;
        }
    });
</script>

<style lang="scss" scoped>
    .pop-enter-active,
    .pop-leave-active {
        transition: opacity 0.5s ease;
    }
    .pop-enter-from,
    .pop-leave-to {
        opacity: 0;
    }
    h3 {
        color: #2c509a;
    }
    .icon {
        height: 20px;
        width: 20px;
        &__instagram {
            height: 34px;
            width: 34px;
        }
    }
    .auth{
        &__wrap {
            height: 44px;
            display: flex;
            justify-content: center;
            margin-bottom:15px;
            &-el {
                background-color: #fef6db;
                width: 44px;
                height: 44px;
                display: flex;
                justify-content: center;
                align-items: center;
                cursor: pointer;
                position: relative;
                user-select: none;
                margin: 0 5px;
                border-radius: 50%;
                &::after {
                    content: '';
                    position: absolute;
                    width: 100%;
                    height: 100%;
                    border-radius: 50%;
                    top: 0;
                    left: 0;
                    background-color: transparent;
                    transition: background-color .2s;
                }
                &:hover::after {
                    background-color: rgba(0,0,0,.1);
                }
            }
         }
        &__with-g {
            background-color: #e9e9e9;
            & img {
                max-width: 100%;
                max-height: 100%;
            }
        }
        &__with-fb {
            background-color: #325fa9;
            color: #fff;
            font-weight: 300;
            & svg {

            }
        }
        &__with-vk {
            background-color: #5b86bb;
            color: #fff;
            font-weight: 300;
            & svg {

            }
        }
    }
    .section-hr {
        margin-bottom: 21px;
        margin-top: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        &__text {
            color:#4a3c3b;
            position:relative;
            font-size: 14px;
            line-height: 17px;
            margin: 0 16px;
        }
       &__line {
           background: #4a3c3b;
           opacity: .3;
           width: 100%;
           height: 1px;
       }

    }
    .form-group {
        margin-bottom: 20px;
        height:100%;
    }

    .form-control {
        outline: medium none #000;
        border: 1px solid #ccc;
        padding: 6px 12px;
        font-size: 16px;
        font-weight: 300;
        width: 100%;
        border-radius: 4px;
        height: 35px;
    }
    .form-forget {
        margin-left: auto;
    }
    .row {
        display: flex;
        justify-content: space-between;
    }
    .btn {
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 40px;
        border: 1px solid #ccc;
        transition: background-color .15s linear;
        border-radius: 4px;
        color: #000;
        text-transform: uppercase;
        font-weight: 600;
    }
    .login {
        background-color: #1867c0;
        color: #fff;
        border: none;
        &:hover {
            background-color: #3b75c0;
        }
    }

    .reg {
        margin-top: 16px;
        background-color: transparent;
        &:hover {
            background-color: #f7f7f7;
        }
    }
</style>
