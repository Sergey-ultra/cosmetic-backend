<template>
    <modal
            v-model:isShowForm="isShowForm"
            :isLoading="isLoadingCurrentCategory"
            :width="50"
    >
        <template v-slot:header>
            {{selectedCategoryId ? 'Редактирование категории' : 'Создание новой категории'}}
        </template>

        <form>
            <div class="input-group">
                <label>
                    Name
                    <input v-model="editedCategory.name" type="text" class="form-control input">
                </label>
            </div>
            <div class="input-group">
                <label>
                    Code
                    <input v-model="editedCategory.code" type="text" class="form-control input">
                </label>
            </div>
            <div class="input-group">
                <label>
                    Desciption
                    <textarea  v-model="editedCategory.description" class="form-control textarea"></textarea>
                </label>
            </div>
            <div class="form-group">
                <div class="form-label table-cell">Фотографии категории</div>
                <div class="table-cell">
                    <one-image-upload v-model:image="editedCategory.image"/>
                </div>
            </div>
        </form>

        <template v-slot:buttons>
            <btn class="left__button" @click="$emit('update:isShowForm', false)">Отмена</btn>
            <btn  @click="save">{{ selectedCategoryId ? 'Редактировать' : 'Создать' }}</btn>
        </template>
    </modal>
</template>

<script>
    import modal from '../../components/modal/modal.vue'
    import oneImageUpload from '../../components/one-image-upload.vue'
    import btn from '../../components/btn.vue'
    import {mapActions, mapState} from "vuex";
    export default {
        name: "category-form",
        components:{
            oneImageUpload,
            modal,
            btn
        },
        data () {
            return {
                editedCategory: {},
            }
        },
        props:{
            selectedCategoryId: Number,
            isShowForm: {
                type:Boolean,
                default: false
            }
        },
        computed: {
            ...mapState('category', ['currentCategory', 'isLoadingCurrentCategory']),
        },
        async created (){
            if (this.selectedCategoryId) {
                await this.loadItem(this.selectedCategoryId)
                this.initEditedObject()
            }
        },
        watch:{
            async selectedCategoryId() {
                if (this.selectedCategoryId) {
                    await this.loadItem(this.selectedCategoryId)
                    this.initEditedObject()
                } else {
                    this.editedCategory = {}
                }
            }
        },
        methods:{
            ...mapActions('category', [ 'loadItem', 'createItem', 'updateItem']),
            initEditedObject() {
                this.editedCategory = {...this.currentCategory }
            },
            async save() {
                if (!this.selectedCategoryId) {
                    await this.createItem(this.editedCategory)
                }  else {
                    await this.updateItem(this.editedCategory)
                }
                this.$emit('update:isShowForm', false)
            },

        }
    }
</script>

<style lang="scss" scoped>
    .input {
        height: 38px;
        width: 200px;
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
    .textarea {
        width: 100%;
        resize: vertical;
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
    .input-group {
        width: 100%;
    }
    .form-control {
        width: 100%;
    }
    .left__button {
        margin-right: 15px;
    }

</style>
