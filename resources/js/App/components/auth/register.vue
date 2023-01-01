<template>
    <form  id="reg-form" @submit.prevent="signUp">
        <div class="form-group">
            <input
                    v-model.trim="registrationForm.name"
                    id="name" type="text"
                    name="name"
                    autofocus
                    placeholder="Имя"
                    class="form-control"
            >
            <div class="invalid-feedback" v-for="error of v$.registrationForm.name.$errors" :key="error.$uid">
                {{ error.$message }}
            </div>
        </div>

        <div class="form-group  mb-1">
            <input
                    v-model.trim="registrationForm.email"
                    id="email" name="email"
                    type="email"
                    placeholder="E-Mail"
                    class="form-control"
            >
            <div class="invalid-feedback" v-for="error of v$.registrationForm.email.$errors" :key="error.$uid">
                {{ error.$message }}
            </div>
        </div>

        <div class="form-group  mb-1">
            <input
                    v-model.trim="registrationForm.password"
                    id="password"
                    type="password"
                    name="password"
                    autocomplete="new-password"
                    placeholder="Пароль"
                    class="form-control"
            >
            <div class="invalid-feedback" v-for="error of v$.registrationForm.password.$errors" :key="error.$uid">
                {{ error.$message }}
            </div>
        </div>

        <div class="form-group  mb-4">
            <input
                    v-model.trim="registrationForm.password_confirmation"
                    id="password-confirmation"
                    type="password"
                    name="password_confirmation"
                    class="form-control"
                    placeholder="Подтвердите пароль"
            >
            <div class="invalid-feedback" v-for="error of v$.registrationForm.password_confirmation.$errors" :key="error.$uid">
                {{ error.$message }}
            </div>
        </div>


        <button class="btn btn-register"  type="submit" >Зарегистрироваться</button>
        <button class="btn btn-back" @click="showLogin">Войти</button>
    </form>
</template>

<script setup>
    import btn from "../../components/btn";
    import useVuelidate from '@vuelidate/core'
    import { required, email, minLength, helpers } from '@vuelidate/validators'
    import {useAuthStore} from "../../store/auth";
    import {defineProps, defineEmits, ref} from "vue";

    const registrationForm = ref({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const rules = {
        registrationForm: {
            name: {
                required:  helpers.withMessage('Поле должно быть заполнено', required),
                minLength: helpers.withMessage('Должно быть не меньше 2 символов', minLength(2))
            },
            email: {
                required:  helpers.withMessage('Поле должно быть заполнено', required),
                email: helpers.withMessage('Не правильно введен email', email)
            },
            password: {
                required:  helpers.withMessage('Поле должно быть заполнено', required),
                minLength: helpers.withMessage('Должно быть не меньше 8 символов', minLength(8))
            },
            password_confirmation: {
                required:  helpers.withMessage('Поле должно быть заполнено', required),
                minLength: helpers.withMessage('Должно быть не меньше 8 символов', minLength(8))
            },
        },
    };

    const v$ = useVuelidate(rules, registrationForm);

    const authStore = useAuthStore();

    const props = defineProps({
        isLoginShow: {
            type:Boolean,
            default: true
        }
    })
    const emit = defineEmits(['update:isLoginShow']);

    const showLogin = () => emit('update:isLoginShow', true);

    const signUp = async () => {
        const validated = await v$.registrationForm.value.$validate();

        if (validated) {
            await authStore.register(registrationForm.value);
            registrationForm.value = {
                name: '',
                email: '',
                password: '',
                password_confirmation: ''
            }
            emit('update:isLoginShow', true);
            v$.value.$reset()
        }
    }
</script>

<style lang="scss" scoped>
    .form-group {
        margin-bottom: 20px;
    }

    .form-control {
        border: 1px solid #ccc;
        padding: 6px 12px;
        font-size: 16px;
        font-weight: 300;
        width: 100%;
        border-radius: 4px;
        height: 35px;
        outline: medium none #000;
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

        &-register {
            background-color: #1867c0;
            color: #fff;
            border: none;

            &:hover {
                background-color: #3b75c0;
            }
        }

        &-back {
            margin-top: 16px;
            background-color: transparent;

            &:hover {
                background-color: #f7f7f7;
            }
        }
    }
    .invalid-feedback {
        color: #fc675c;
    }
</style>