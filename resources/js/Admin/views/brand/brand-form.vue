<template>
    <modal
            v-model:isShowForm="isShowForm"
            :isLoading="isLoadingCurrentBrand"
            :width="60"
    >
        <template v-slot:header>
            {{selectedBrandId ? 'Редактирование бренда' : 'Создание нового бренда'}}
        </template>

        <form @submit.prevent="" class="form">
            <div class="form__inputs">
                <div class="input__group">
                   <div class="input__item">Country</div>
                   <div class="input__item">
                       <select v-model="editedBrand.country_id" class="form__control">
                           <option value="null">Выберите страну</option>
                           <option v-for="(country,index) in allCountries"
                                   :value="country.id"
                                   :key="index">{{ country.name }}
                           </option>
                       </select>
                   </div>
                </div>

                <div class="input__group">
                    <div class="input__item">Имя</div>
                    <div class="input__item">
                        <input v-model="editedBrand.name" type="text" class="form__control input">
                    </div>
                </div>

                <div class="input__group">
                    <div class="input__item">Описание</div>
                    <div class="input__item">
                        <textarea v-model="editedBrand.description" class="form__control textarea"></textarea>
                    </div>
                </div>
            </div>


            Фотографии товара
            <one-image-upload v-model:image="editedBrand.image"/>

        </form>

        <template v-slot:buttons>
            <btn class="button" @click="$emit('update:isShowForm', false)">Отмена</btn>
            <btn @click="save">{{ selectedBrandId ? 'Редактировать' : 'Создать' }}</btn>
        </template>
    </modal>
</template>

<script>
    import oneImageUpload from '../../components/one-image-upload.vue'
    import modal from '../../components/modal/modal.vue'
    import btn from '../../components/btn.vue'
    import {mapActions, mapState} from "vuex";

    export default {
        name: "brand-form",
        components: {
            oneImageUpload,
            btn,
            modal
        },
        props: {
            selectedBrandId: Number,
            isShowForm: Boolean
        },
        data (){
            return {
                editedBrand: {}
            }
        },
        computed: {
            ...mapState('country', ['allCountries']),
            ...mapState('brand', ['isLoadingCurrentBrand', 'loadedBrand']),
        },
        async created() {
            if (!this.allCountries.length) {
                this.loadAllCountries()
            }
            if (this.selectedBrandId) {
                await this.loadItem(this.selectedBrandId)
                this.initEditedObject()
            }
        },
        watch: {
            async selectedBrandId(value) {
                if (value) {
                    await this.loadItem(value)
                    this.initEditedObject()
                } else {
                    this.editedBrand = {}
                }
            },
        },
        methods: {
            ...mapActions('country', ['loadAllCountries']),
            ...mapActions('brand', ['loadItem', 'createItem', 'updateItem']),
            initEditedObject() {
                this.editedBrand = {...this.loadedBrand }
            },
            async save() {
                if (!this.selectedBrandId) {
                    await this.createItem(this.editedBrand)
                }  else {
                    await this.updateItem(this.editedBrand)
                }
                this.$emit('update:isShowForm', false)
            },
        }
    }
</script>

<style scoped lang="scss">
    .form {
        &__inputs {
            width: 400px;
        }

        &__control {
            width: 100%;
        }
    }
    .input {
        &__group {
            flex-wrap: wrap;
            justify-content: center;
        }
        &__item {
            width: 100%;
            margin-bottom: 10px;
        }
    }
    .input {
        background-color: #f0f2fc;
        border: 1px solid transparent;
        border-radius: 8px;
        outline: medium none #000;
        overflow: visible;
        padding: 8px;
        transition: background-color .3s ease 0s,border-color .3s ease 0s;

        &:hover {
            border-color: #c0c9f0;
            transition: border-color .3s ease 0s;
        }
    }
    .textarea {
        width: 100%;
        resize: vertical;
        outline: #000 none medium;
        overflow: visible;
        transition: background-color 0.3s ease 0s, border-color 0.3s ease 0s;
        border: 1px solid transparent;
        border-radius: 8px;
        padding: 8px;
        background-color: #f0f2fc;
        &:hover {
            border-color: #c0c9f0;
            transition: border-color .3s ease 0s;
        }
    }
    .button {
        &:not(:last-child) {
            margin-right: 15px;
        }
    }
</style>
