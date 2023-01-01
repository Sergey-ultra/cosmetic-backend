<template>
    <modal
            v-model:isShowForm="isShowForm"
            :width="60"
    >
        <template v-slot:header>
           {{ selectedStoreId ? 'Редактирование магазина' : 'Создание нового магазина' }}
        </template>
        <form class="form">
            <div class="form__inputs">
                <div class="input__group">
                    <div class="input__item">Name</div>
                    <div class="input__item">
                        <input v-model="editedStore.name" type="text" class="form__control  input">
                    </div>
                </div>

                <div class="input__group">
                    <div class="input__item">Link</div>
                    <div class="input__item">
                        <input v-model="editedStore.link" type="text" class="form__control input">
                    </div>
                </div>
            </div>

            Фотографии товара
            <one-image-upload v-model:image="editedStore.image"/>
        </form>

        <template v-slot:buttons>
            <btn class="button" @click="$emit('update:isShowForm', false)">Отмена</btn>
            <btn  @click="save">{{ selectedStoreId ? 'Сохранить' : 'Создать' }}</btn>
        </template>
    </modal>
</template>

<script>
    import oneImageUpload from '../../components/one-image-upload.vue'
    import modal from '../../components/modal/modal.vue'
    import btn from '../../components/btn.vue'
    import {mapActions, mapState} from "vuex";

    export default {
        name: "store-form",
        components:{
            oneImageUpload,
            btn,
            modal
        },
        props: {
            selectedStoreId: Number,
            isShowForm: Boolean
        },
        data() {
            return {
                editedStore: {}
            }
        },
        computed: {
            ...mapState('store', ['loadedStore']),
        },
        async created() {
            if (this.selectedStoreId) {
                await this.loadItem(this.selectedStoreId)
                this.initEditedObject()
            }
        },
        watch:{
            async selectedStoreId(value) {
                if (value) {
                    await this.loadItem(value)
                    this.initEditedObject()
                } else {
                    this.editedStore = {}
                }
            }
        },
        methods:{
            ...mapActions('store', [ 'loadItem', 'createItem', 'updateItem']),
            initEditedObject() {
                this.editedStore = {...this.loadedStore }
            },
            async save() {
                if (!this.selectedStoreId) {
                    await this.createItem(this.editedStore)
                }  else {
                    await this.updateItem(this.editedStore)
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
    .button {
        &:not(:last-child) {
            margin-right: 15px;
        }
    }
</style>
