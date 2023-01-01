<template>
    <modal
            v-model:isShowForm="isShowForm"
            :width="50"
    >
        <template v-slot:header>
           {{selectedReviewId ? 'Редактирование отзывы' : 'Создание нового отзыва'}}
        </template>

        <form class="form">
            <div class="form__inputs">
                <div class="input__group">
                    <div class="input__item">Комментарий</div>
                    <div class="input__item">
                        <textarea v-model="editedReview.comment"  class="form__control  textarea"></textarea>
                    </div>
                </div>

                <div class="input__group">
                    <div class="input__item">Плюсы</div>
                    <div class="input__item">
                        <textarea v-model="editedReview.plus"  class="form__control  textarea"></textarea>
                    </div>
                </div>

                <div class="input__group">
                    <div class="input__item">Минусы</div>
                    <div class="input__item">
                        <textarea v-model="editedReview.minus" class="form__control textarea"></textarea>
                    </div>
                </div>

                <div class="input__group">
                    <div class="input__item">
                        <span>Анонимно?</span>
                        <input v-model="anonymouslyLocal" type="checkbox" class="form__control">
                    </div>
                </div>

                <div class="input__group">
                    <div class="input__item">Статус</div>
                    <div class="input__item">
                        <select v-model="editedReview.status"  class="form__control">
                            <option value="moderated">Moderated</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                </div>
            </div>

            Фотографии отзыва
            <multiple-image-upload v-model:images="editedReview.images"
            />
        </form>

        <template v-slot:buttons>
            <btn class="button" @click="$emit('update:isShowForm', false)">Отмена</btn>
            <btn  @click="save">{{ selectedReviewId ? 'Сохранить' : 'Создать' }}</btn>
        </template>
    </modal>
</template>

<script>
    import modal from '../../components/modal/modal.vue'
    import multipleImageUpload from '../../components/multiple-image-upload.vue'
    import btn from '../../components/btn.vue'
    import {mapActions, mapState} from "vuex";

    export default {
        name: "review-form",
        components: {
            modal,
            btn,
            multipleImageUpload,
        },
        data() {
            return {
                editedReview: {}
            }
        },
        props: {
            selectedReviewId: Number,
            isShowForm: {
                type:Boolean,
                default: false
            }
        },
        computed: {
            ...mapState('review', ['loadedReview']),
            anonymouslyLocal: {
                get() {
                    return Boolean(this.editedReview.anonymously)
                },
                set(value) {
                    this.editedReview.anonymously = Number(value)
                }
            }
        },
        async created() {
            if (this.selectedReviewId) {
                await this.loadItem(this.selectedReviewId)
                this.initEditedObject()
            }
        },
        watch: {
            async selectedReviewId(value) {
                if (value) {
                    await this.loadItem(value)
                    this.initEditedObject()
                } else {
                    this.editedReview = {}
                }
            }
        },
        methods: {
            ...mapActions('review', ['loadItem', 'createItem', 'updateItem']),
            initEditedObject() {
                this.editedReview = {
                    ...this.loadedReview,
                    images: Array.isArray(this.loadedReview.images)
                        ? [...this.loadedReview.images]
                        : []
                }
            },
            async save() {
                if (!this.selectedReviewId){
                    await this.createItem(this.editedReview)
                } else {
                    await this.updateItem(this.editedReview)
                }
                this.$emit('update:isShowForm', false)
            }
        }
    }
</script>

<style scoped lang="scss">
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
