<template>
    <modal
            v-model:isShowForm="isShowForm"
            :isLoading="isLoadingCurrentIngredient"
            :width="60"
    >
        <template v-slot:header>
           {{selectedIngredientId ? 'Редактирование ингредиента' : 'Создание нового ингредиента'}}
        </template>

        <form  @submit.prevent="" class="form">
            <div class="form__inputs">
                <div class="input__group">
                    <div class="input__item">Name</div>
                    <div class="input__item">
                        <input v-model="editedIngredient.name" type="text" class="form__control input">
                    </div>

                </div>
                <div class="input__group">
                    <div class="input__item">Name_rus</div>
                    <div class="input__item">
                        <input v-model="editedIngredient.name_rus" type="text" class="form__control input">
                    </div>
                </div>

                <div class="input__group">
                    <div class="input__item">Description</div>
                    <div class="input__item">
                        <textarea v-model="editedIngredient.description" class="form__control textarea"></textarea>
                    </div>
                </div>
            </div>
        </form>

        <template v-slot:buttons>
            <btn class="left__button" @click="$emit('update:isShowForm', false)">Отмена</btn>
            <btn  @click="save">{{ selectedIngredientId ? 'Сохранить' : 'Создать' }}</btn>
        </template>
    </modal>
</template>

<script>
    import modal from '../../components/modal/modal.vue'
    import btn from '../../components/btn.vue'
    import {mapActions, mapState} from "vuex";
    export default {
        name: "ingredient-form",
        components:{
            btn,
            modal
        },
        props: {
            selectedIngredientId: Number,
            isShowForm: Boolean
        },
        data() {
            return {
                editedIngredient: {}
            }
        },
        computed: {
            ...mapState('ingredient', ['isLoadingCurrentIngredient', 'loadedIngredient']),
        },
        async created() {
            if (this.selectedIngredientId) {
                await this.loadItem(this.selectedIngredientId)
                this.initEditedObject()
            }
        },
        watch:{
            async selectedIngredientId() {
                if (this.selectedIngredientId) {
                    await this.loadItem(this.selectedIngredientId)
                    this.initEditedObject()
                } else {
                    this.editedIngredient = {}
                }
            },
        },
        methods:{
            ...mapActions('ingredient', [ 'loadItem', 'createItem', 'updateItem']),
            initEditedObject() {
                this.editedIngredient = {...this.loadedIngredient }
                console.log(this.editedIngredient)
            },
            async save() {
                if (!this.selectedIngredientId) {
                    await this.createItem(this.editedIngredient)
                } else {
                    await this.updateItem(this.editedIngredient)
                }
                this.$emit('update:isShowForm', false)
            }
        }
    }
</script>

<style lang="scss" scoped>
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
    .left__button {
        margin-right: 15px;
    }
</style>
