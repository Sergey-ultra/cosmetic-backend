<template>
    <footer class="footer">
        <div class="container">
            <div class="footer__main">
                <div class="footer__left">
                    <div class="footer__col">
                        <div class="footer__title">О компании</div>
                        <div class="footer__item">
                            <router-link :to="{ name: 'about'}"> Вопросы и пожелания по сайту</router-link>
                        </div>
                        <div class="footer__item">
                            <router-link :to="{ name:'policy' }">Политика конфиденциальности</router-link>
                        </div>
                    </div>
                    <div class="footer__col">
                        <div class="footer__title">Сотрудничество</div>
                        <div class="footer__item">
                            <a href="/supplier">Личный кабинет поставщика</a>
                        </div>
                    </div>
                </div>
                <div class="footer__right">
                    <div class="footer__col">
                        <div class="footer__title"> Следите за новинками и акциями:</div>
                        <form class="main__style subscription" @submit.prevent="createSubscription">
                            <div class="subscription__form">
                                <div class="subscription__form-item">
                                    <input type="text" class="input" placeholder="Ваш email" v-model.trim="mail">
                                    <div class="invalid-feedback" v-for="error of v$.mail.$errors" :key="error.$uid">
                                        {{ error.$message }}
                                    </div>
                                </div>
                                <div class="subscription__form-item subscription__form-submit">
                                    <button  class="subscription__button" type="submit">ПОДПИСАТЬСЯ</button>
                                </div>
                            </div>
                            <div class="subscription__policy">
                                Нажимая на кнопку «Подписаться», я соглашаюсь с
                                <router-link :to="{ name:'policy' }" target="_blank">Политикой конфиденциальности</router-link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div>Нашли ошибку на этой странице? Выделите ее и нажмите Ctrl+Enter</div>
            <div>По вопросам сотрудничества admin@smart-beautiful.ru</div>
            <div class="footer__copy">{{ new Date().getFullYear() }} © Smart-Beautiful | Мониторинг цен косметических товаров</div>
        </div>
    </footer>
</template>

<script setup>
    import useVuelidate from '@vuelidate/core';
    import { required, email, helpers } from '@vuelidate/validators';
    import {useSubscriptionStore} from "../../store/subscription";
    import {ref} from "vue";

    const subscriptionStore = useSubscriptionStore();

    let mail = ref('');
    const rules = {
        mail: {
            required: helpers.withMessage('Поле должно быть заполнено', required),
            email: helpers.withMessage('Введите правильный email', email)
        }
    };

    const v$ = useVuelidate(rules, {mail});

    const createSubscription = async () => {
        const validated = await v$.value.mail.$validate();
        if (validated) {
            await subscriptionStore.create({ email: mail.value });
            v$.value.$reset();
            mail.value = '';
        }
    };
</script>

<style lang="scss" scoped>
    .invalid-feedback {
        color: #fff;
    }
    .footer {
        background: #333;
        color: #fff;
        & a {
            color: #fff;
        }
        &__main {
            margin-bottom:20px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        &__left {
            display: flex;
            flex-wrap:wrap;
        }
        &__right {
            width: 340px;
        }

        &__col {
            font-size: 18px;
            padding: 15px 0;

            &:not(:last-child) {
                margin-right: 49px;
            }
        }

        &__title {
            padding: 0 0 20px;
            font-weight: 700;
            font-size: 20px;
            line-height: 20px;
            margin: 20px 0 16px;
            color: #fff;
            border-bottom: 1px solid #4e4e4e;
        }
        &__item {
            margin-bottom: 12px;
            &:not(:last-child) {
                margin-right: 25px;
            }
            & a {
                text-decoration: none;
            }
            &:hover a {
                color: #ebeff4;
            }
        }
        &__copy {
            border-top: 1px solid #444;
            margin: 15px auto;
        }
    }
    .subscription {
        color: #000;
        &__button {
            height: 38px;
            background-color: #e8e8e8;
            border: none;
            color: #333;
            font-weight:bold;
            border-radius: 8px;
            transition: background-color 0.3s ease 0s, border-color 0.3s ease 0s;
            padding: 0 30px;
            &:hover {
                background-color: #dcdcdc;
            }
        }
        &__form {
            margin: 0 auto;
            max-width: 700px;
            justify-content: space-between;
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            &-item {
                margin-bottom: 20px;
                &:not(:last-child) {
                    margin-right: 10px;
                }

                & .input {
                    outline: #000 none medium;
                    overflow: visible;
                    transition: background-color 0.3s ease 0s, border-color 0.3s ease 0s;
                    border: 1px solid transparent;
                    border-radius: 8px;
                    padding: 8px;
                    background-color: rgb(240, 242, 252);
                    &:hover {
                        border-color: rgb(192, 201, 240);
                        transition: border-color 0.3s ease 0s;
                    }
                    &:focus {
                        background-color: white;
                        border-color: rgb(59, 87, 208);
                        transition: background-color 0.3s ease 0s, border-color 0.3s ease 0s;
                    }
                }
            }
        }
        &__policy {
            color: #8c8c8c;
            font-size: 12px;
            line-height: 16px;
            margin-top: 10px;
        }
    }

    @media (max-width: 700px) {
        .subscription {
            &__form {
                /*justify-content: center;*/
                &-submit {
                    /*width: 100%;*/
                }
            }
        }
    }
</style>