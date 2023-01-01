<template>
    <form  v-if="myStore.store_id === null" class="form" @submit.prevent="editStore">
        <div class="form__item">
            <input class="input" type="text" v-model="storeForm.name"
                   placeholder="имя магазина">
            <div class="invalid-feedback" v-for="error of v$.storeForm.name.$errors"
                 :key="error.$uid">
                {{ error.$message }}
            </div>
        </div>


        <div class="form__item">
            <input class="input" type="text" v-model="storeForm.link"
                   placeholder="URL магазина">
            <div class="invalid-feedback" v-for="error of v$.storeForm.link.$errors"
                 :key="error.$uid">
                {{ error.$message }}
            </div>
        </div>


        <div class="form__item">
            <input class="input" type="text" v-model="storeForm.file_url"
                   placeholder="URL прайса">
            <div class="invalid-feedback" v-for="error of v$.storeForm.file_url.$errors"
                 :key="error.$uid">
                {{ error.$message }}
            </div>
        </div>

        Логотип
        <one-image-upload v-model:image="storeForm.image" class="form__item"/>



        <div class="form__item">
            <button class="form__button" type="submit">Редактировать</button>
        </div>
    </form>
    <form  v-else class="form" @submit.prevent="editLink">
        <div class="form__item">
            <input class="input" type="text" v-model="linkForm.file_url"
                   placeholder="URL прайса">
            <div class="invalid-feedback" v-for="error of v$.linkForm.file_url.$errors"
                 :key="error.$uid">
                {{ error.$message }}
            </div>
        </div>
        <div class="form__item">
            <button class="form__button" type="submit">Редактировать</button>
        </div>
    </form>
</template>

<script>

import useVuelidate from "@vuelidate/core";
import {helpers, required} from "@vuelidate/validators";
import {mapActions, mapState} from "vuex";
import oneImageUpload from "../../components/one-image-upload.vue";

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
    name: "edit-store",
    components: {
      oneImageUpload
    },
    setup () {
        return { v$: useVuelidate() }
    },
    validations () {
        return {
            storeForm: {
                name: { required:  helpers.withMessage('Поле должно быть заполнено', required)},
                link: { required:  helpers.withMessage('Поле должно быть заполнено', required)},
                file_url: {
                    required:  helpers.withMessage('Поле должно быть заполнено', required),
                    isUrl:  helpers.withMessage('Строка должна быть ссылкой на файл прайсов', isUrl)
                }
            },
            linkForm : {
                file_url: {
                    required:  helpers.withMessage('Поле должно быть заполнено', required),
                    isUrl:  helpers.withMessage('Строка должна быть ссылкой на файл прайсов', isUrl)
                }
            }
        }
    },
    data() {
        return {
            storeForm: {
                name:'',
                link:'',
                file_url: '',
                image: ''
            },
            linkForm : {
                file_url: '',
            }
        }
    },
    computed: {
        ...mapState('store', ['myStore'])
    },
    async created() {
        if (!this.myStore) {
            await this.loadMyStore()
        }
        if (this.myStore.store_id === null) {
            this.storeForm = { ...this.myStore }
        } else {
            this.linkForm = { id: this.myStore.id, file_url: this.myStore.file_url }
        }

    },
    methods: {
        ...mapActions('store', ['updateStore', 'loadMyStore']),
        async editStore() {
            const validated = await this.v$.storeForm.$validate()
            if (validated) {
                await this.updateStore(this.storeForm)
                this.v$.$reset()
                this.storeForm = {
                    name: '',
                    link: '',
                    file_url: '',
                    image: ''
                }
                this.$router.push({ name: 'store' })
            }
        },
        async editLink() {
            const validated = await this.v$.linkForm.$validate()
            if (validated) {
                await this.updateStore(this.linkForm)
                this.v$.$reset()
                this.linkForm = {
                    file_url: ''
                }
                this.$router.push({ name: 'store' })
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
