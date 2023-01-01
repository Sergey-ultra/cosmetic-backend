<template>
    <modal
            v-model:isShowForm="isShowForm"
            :width="50"
    >
        <template v-slot:header>
           {{selectedUserId ? 'Редактирование ' : 'Создание нового пользователя'}}
        </template>

        <form class="form">
            <div class="form__inputs">
                <div class="input__group">
                    <div class="input__item">Name</div>
                    <div class="input__item">
                        <input v-model="editedUser.name" type="text" class="form__control input">
                    </div>
                </div>

                <div class="input__group">
                    <div class="input__item">Email</div>
                    <div class="input__item">
                        <input  v-model="editedUser.email" type="text" class="form__control input"/>
                    </div>
                </div>

                <div class="input__group">
                    <div class="input__item">Роль</div>
                    <div class="input__item">
                        <select v-model="editedUser.role_id" class="form__control">
                            <option value="null">Выберите</option>
                            <option
                                    v-for="role in availableRoles"
                                    :key="role.id"
                                    :value="role.id"
                            >
                                {{ role.name }}
                            </option>
                         </select>
                    </div>
                </div>
            </div>
        </form>

        <template v-slot:buttons>
            <btn class="button" @click="$emit('update:isShowForm', false)">Отмена</btn>
            <btn  @click="save">{{ selectedUserId ? 'Редактировать' : 'Создать' }}</btn>
        </template>
    </modal>
</template>

<script>
    import modal from '../../components/modal/modal.vue'
    import imagesUpload from '../../components/image-upload-as-form/image-upload.vue'
    import btn from '../../components/btn.vue'
    import {mapActions, mapState} from "vuex";

    export default {
        name: "user-form",
        components: {
            imagesUpload,
            modal,
            btn
        },
        data() {
            return {
                editedUser: {}
            }
        },
        props: {
            selectedUserId: Number,
            isShowForm: {
                type:Boolean,
                default: false
            }
        },
        computed: {
            ...mapState('user', ['loadedUser', 'availableRoles']),
            ...mapState('image', ['uploadingImageUrls'])

        },
        async created() {
            if (!this.availableRoles.length) {
                this.loadAvailableRoles()
            }

            if (this.selectedUserId) {
                await this.loadItem(this.selectedUserId)
                this.initEditedObject()
            }
        },
        watch: {
            async selectedUserId() {
                if (this.selectedUserId) {
                    await this.loadItem(this.selectedUserId)
                    this.initEditedObject()
                } else {
                    this.editedUser = {}
                }
            },
            uploadingImageUrls(value) {
                if (value.length) {
                    this.editedStore.image = value[0]
                }
            }
        },
        methods: {
            ...mapActions('user', ['loadItem', 'createItem', 'updateItem', 'loadAllRoles', 'loadAvailableRoles']),
            initEditedObject() {
                this.editedUser = {...this.loadedUser }
            },
            async save() {
                if (!this.selectedUserId)  {
                    await this.createItem(this.editedUser)
                }  else {
                    await this.updateItem(this.editedUser)
                }
                this.$emit('update:isShowForm', false)
            },
            deleteFromEditedImageUrl() {
                this.editedBrand.image = null
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
