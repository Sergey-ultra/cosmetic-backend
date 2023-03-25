<template>
    <div class="modal-layer">
        <transition name="popup" mode="out-in">
            <div class="modal-dialog">
                <div class="modal__header">
                    <div class="modal__title">Логин</div>
                </div>

                <div class="modal__body">
                    <form id="log-form" >
                        <div class="form__group">
                            <input
                                    v-model.trim="loginForm.email"
                                    type="email"
                                    name="email"
                                    class="form__control"
                                    placeholder="Email"
                            >
                            <div class="invalid-feedback" v-for="error of v$.loginForm.email.$errors" :key="error.$uid">
                                {{ error.$message }}
                            </div>
                        </div>

                        <div class="form__group">
                            <input
                                    v-model.trim="loginForm.password"
                                    type="password"
                                    name="password"
                                    class="form__control"
                                    placeholder="Пароль"
                                    id="password"
                                    autocomplete="current-password"
                            >
                            <div class="invalid-feedback" v-for="error of v$.loginForm.password.$errors" :key="error.$uid">
                                {{ error.$message }}
                            </div>
                        </div>



                        <div class="form__group">
                            <div class="row">
                                <buttonComponent :color="'blue'" @click="signIn">Логин</buttonComponent>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
    import {mapActions} from "vuex";
    import useVuelidate from "@vuelidate/core";
    import {email, helpers, minLength, required} from "@vuelidate/validators";
    import buttonComponent from "../button-component.vue";

    export default {
        name: "login",
        components: {
            buttonComponent
        },
        setup () {
            return { v$: useVuelidate() }
        },
        validations () {
            return {
                loginForm: {
                    email: {
                        required:  helpers.withMessage('Поле должно быть заполнено', required),
                        email: helpers.withMessage('Не правильно введен email', email)
                    },
                    password: {
                        required:  helpers.withMessage('Поле должно быть заполнено', required),
                        minLength: helpers.withMessage('Должно быть не меньше 8 символов', minLength(8))
                    }
                }
            }
        },
        data() {
            return {
                loginForm: {
                    email: '',
                    password: '',
                }
            }
        },
        methods: {
            ...mapActions('auth', ['login']),
            async signIn() {
                const validated = await this.v$.loginForm.$validate()

                if (validated) {
                    await this.login(this.loginForm)
                    this.loginForm = {
                        email: '',
                        password: '',
                    }
                    this.v$.$reset()
                }
            }
        }
    }
</script>

<style lang="scss" scoped>
    .modal-layer {
        background-color: rgba(33, 33, 33, 0.46);
        display: block;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 70;
        width: 100%;
        height: 100%;
        overflow: hidden;
        outline: 0;
    }

    .popup-enter-active,
    .popup-leave-active {
        transition: all 0.4s ease;
    }

    .popup-enter-from,
    .popup-leave-to {
        transform: translateY(-100px);
    }

    .modal-dialog {
        position: absolute;
        width: 35%;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        border-radius: 5px;
        background-color: #fff;
    }

    .modal__header {
        padding: 1rem 1rem;
        border-bottom: 1px solid #dee2e6;
    }

    .modal__title {
        font-size: 20px;
    }

    .modal__body {
        padding: 1rem 1rem;
    }

    .row {
        display: flex;
        justify-content: center;
    }
    .form {
        &__group {
            width: 100%;
            &:not(:last-child) {
                margin-bottom: 15px;
            }
        }
        &__control {
            width: 100%;
            outline: #000 none medium;
            overflow: visible;
            transition: background-color 0.3s ease 0s, border-color 0.3s ease 0s;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 8px;
            &:hover {
                border-color: rgb(192, 201, 240);
                transition: border-color 0.3s ease 0s;
            }
            &:focus {
                border-color: rgb(59, 87, 208);
                transition: background-color 0.3s ease 0s, border-color 0.3s ease 0s;
            }
        }

    }
    .invalid-feedback {
        color: red;
    }
</style>
