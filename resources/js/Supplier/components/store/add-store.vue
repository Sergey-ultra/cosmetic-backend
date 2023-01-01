<template>
    <form class="form" @submit.prevent="createNewStore">

        <div class="form__item">
            <input class="input" type="text" v-model="storeForm.name"
                   placeholder="имя магазина">
            <div class="invalid-feedback" v-for="error of v$.storeForm.name.$errors"
                 :key="error.$uid">
                {{ error.$message }}
            </div>
        </div>

        <div class="form__item">
            <input class="input" type="text" v-model="storeForm.store_url"
                   placeholder="URL магазина">
            <div class="invalid-feedback" v-for="error of v$.storeForm.store_url.$errors"
                 :key="error.$uid">
                {{ error.$message }}
            </div>
        </div>

        <div class="form__item">
            <input class="input" type="text" v-model="storeForm.price_url"
                   placeholder="URL прайса">
            <div class="invalid-feedback" v-for="error of v$.storeForm.price_url.$errors"
                 :key="error.$uid">
                {{ error.$message }}
            </div>
        </div>


        <div class="form__item" @click="$emit('update:isShowCreateForm', false)">
            <button class="form__button" type="button">Назад</button>
        </div>
        <div class="form__item">
            <button class="form__button" type="submit">Создать новый магазин</button>
        </div>
    </form>
</template>

<script>
import useVuelidate from "@vuelidate/core";
import {helpers, required} from "@vuelidate/validators";
import {mapActions} from "vuex";

const isUrl = string => {
    let url
    try {
        url = new URL(string);
    } catch (e) {
        return false;
    }

    return url.protocol === "http:" || url.protocol === "https:";
}

export default {
    name: "add-store",
    setup () {
        return { v$: useVuelidate() }
    },
    validations () {
        return {
            storeForm:{
                name: {required:  helpers.withMessage('Поле должно быть заполнено', required)},
                store_url: {required:  helpers.withMessage('Поле должно быть заполнено', required)},
                price_url: {
                    required:  helpers.withMessage('Поле должно быть заполнено', required),
                    isUrl:  helpers.withMessage('Строка должна быть ссылкой на файл прайсов', isUrl)
                },
            }
        }
    },
    data() {
        return {
            storeForm: {
                name:'',
                store_url:'',
                price_url: ''
            }
        }
    },
    methods: {
        ...mapActions('store', ['createStore']),
        async createNewStore() {
            const validated = await this.v$.storeForm.$validate()
            if (validated) {
                await this.createStore(this.storeForm)
                this.v$.$reset()
                this.storeForm = {
                    name: '',
                    store_url: '',
                    price_url: ''
                }
            }
        }
    }
}
</script>

<style scoped lang="scss">
    .invalid-feedback {
        color: #fc675c;
    }
    .form {
        width: 600px;
        &__row {
            width:100%;
            display: flex;
            justify-content: center;
            flex-wrap:wrap;

        }
        &__item {
            width: 100%;
            margin-bottom: 20px;
        }
        &__button {
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 40px;
            border: 1px solid #ccc;
            border-radius: 4px;
            color: #000;
            text-transform: uppercase;
            font-weight:bold;
            background-color: #fff;
            transition: background-color 0.3s ease 0s, border-color 0.3s ease 0s;
            padding: 0 30px;
            &:hover {
                background-color: #dcdcdc;
            }
            &-active {
                background-color: #1867c0;
                color: #fff;
                border: none;
                &:hover {
                    background-color: #3b75c0;
                }
            }
        }
    }


    .input {
        display: block;
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
</style>