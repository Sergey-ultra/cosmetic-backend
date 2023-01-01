<template>
    <modal
            v-model:isShowForm="isShowForm"
            :isLoading="isLoadingCurrentSku"
            :width="60"
    >
        <template v-slot:header>
         {{selectedSkuId ? 'Редактирование товарного предложения' : 'Создание нового товарного предложения'}}
        </template>
        <form class="form">
            <div class="form__inputs">

                <div class="input__group">
                    <div class="input__item">Category</div>
                    <div class="input__item">
                        <select v-model="editedSku.category_id" class="form__control">
                            <option
                                    v-for="category in allCategories"
                                    :value="category.id"
                                    :key="category.id"
                            >
                                {{category.name}}
                            </option>
                        </select>
                    </div>
                </div>

                 <div class="input__group">
                    <div class="input__item">Brand</div>
                    <div class="input__item">
                        <select v-model="editedSku.brand_id" class="form__control">
                            <option
                                    v-for="brand in allBrands"
                                    :value="brand.id"
                                    :key="brand.id"
                            >
                                {{ brand.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="input__group">
                    <div class="input__item">Name</div>
                    <div class="input__item">
                        <input v-model="editedSku.name" type="text" class="form__control input">
                    </div>
                </div>

                <div class="input__group">
                    <div class="input__item">Объем</div>
                    <div class="input__item">
                        <input v-model="editedSku.volume" type="text" class="form__control input">
                    </div>
                </div>

                <div class="input__group">
                    <div class="input__item">Description</div>
                    <div class="input__item">
                        <textarea v-model="editedSku.description" class="form__control textarea"></textarea>
                    </div>
                </div>
            </div>

            <expansion-panel class="form__item">
                Ingredients
                <div >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                         class="bi bi-pencil-fill" viewBox="0 0 16 16">
                        <path
                                d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                    </svg>
                </div>
                <template v-slot:content>
                    <ingredient-list :ingredients="editedSku.ingredients"/>
                </template>
            </expansion-panel>


                Фотографии товара

                    <multiple-image-upload
                        v-model:images="editedSku.images"
                    />


        </form>
        <template v-slot:buttons>
            <btn class="button" @click="$emit('update:isShowForm', false)">Отмена</btn>
            <btn  @click="save">{{ selectedSkuId ? 'Сохранить' : 'Создать' }}</btn>
        </template>
    </modal>
</template>

<script>
    import modal from '../../components/modal/modal.vue'
    import expansionPanel from '../../components/expansion-panel.vue'
    import multipleImageUpload from '../../components/multiple-image-upload.vue'
    import btn from '../../components/btn.vue'
    import ingredientList from './ingredient-list.vue'
    import {mapActions, mapState} from "vuex";

    export default {
        name: "product-form",
        components: {
            modal,
            btn,
            multipleImageUpload,
            ingredientList,
            expansionPanel
        },
        data() {
            return {
                editedSku: {}
            }
        },
        props: {
            selectedSkuId: Number,
            isShowForm: Boolean
        },
        computed: {
            ...mapState('brand', ['allBrands']),
            ...mapState('category', ['allCategories']),
            ...mapState('sku', ['isLoadingCurrentSku', 'loadedSku'])
        },
        async created() {
            if (!this.allCategories.length) {
                this.loadAllCategories()
            }
            if (!this.allBrands.length) {
                this.loadAllBrands()
            }

            if (this.selectedSkuId) {
                await this.loadItem(this.selectedSkuId)
                this.initEditedObject()
            }
        },
        watch: {
            async selectedSkuId(value) {
                if (value) {
                    await this.loadItem(value)
                    this.initEditedObject()
                } else {
                    this.editedSku = {}
                }
            },
        },
        methods: {
            ...mapActions('sku', ['loadItem', 'createItem', 'updateItem']),
            ...mapActions('brand', ['loadAllBrands']),
            ...mapActions('category', ['loadAllCategories']),
            initEditedObject() {
                this.editedSku = {
                    ...this.loadedSku,
                    images: Array.isArray(this.loadedSku.images)
                        ? [...this.loadedSku.images]
                        : []
                }
            },
            async save() {
                if (!this.selectedSkuId){
                    await this.createItem(this.editedSku)
                } else {
                    await this.updateItem(this.editedSku)
                }
                this.$emit('update:isShowForm', false)
            }
        }
    }
</script>

<style lang="scss" scoped>
    .form {
        &__inputs {
            width: 500px;
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
