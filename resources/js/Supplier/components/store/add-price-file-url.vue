<template>
    <form class="form" @submit.prevent="saveStoreUrl">

        <div class="form__row">
            <div class="form__item">
                <input class="input" type="text" v-model="priceUrl"
                       placeholder="URL прайса">
                <div class="invalid-feedback" v-for="error of v$.priceUrl.$errors"
                     :key="error.$uid">
                    {{ error.$message }}
                </div>
            </div>
        </div>

        <div class="form__item form__row" @click="$emit('update:isShowStoreUrlForm', false)">
            <button class="form__button" type="button">Назад</button>
        </div>
        <div class="form__item form__row">
            <button class="form__button" type="submit">Подать заявку на размещение</button>
        </div>
    </form>
</template>

<script>
import {helpers, required} from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import {mapActions, mapState} from "vuex";

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
    name: "add-price-file-url",
    setup () {
        return { v$: useVuelidate() }
    },
    validations () {
        return {
            priceUrl: {
                required:  helpers.withMessage('Поле должно быть заполнено', required),
                isUrl:  helpers.withMessage('Строка должна быть ссылкой на файл прайсов', isUrl)
            },
        }
    },
    data() {
        return {
            priceUrl: ''
        }
    },
    props: {
      storeId: Number
    },
    computed: {
        ...mapState('store', ['isAddingPriceFile'])

    },
    methods: {
        ...mapActions('store', ['addPriceFile']),
        async saveStoreUrl() {
            const validated = await this.v$.priceUrl.$validate()
            if (validated) {
                await this.addPriceFile({store_id: this.storeId, price_url: this.priceUrl})
                this.v$.$reset()
                this.priceUrl = ''
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
        margin-bottom: 20px;
    }
    &__item {
        width: 100%;
        &:not(:last-child) {
            margin-right: 10px;
        }
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