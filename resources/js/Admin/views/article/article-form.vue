<template>
    <form class="form">
        <div>Заглавная картинка статьи</div>
        <one-image-upload v-model:image="editedArticle.image"/>
        <div class="invalid-feedback" v-for="error of v$.editedArticle.image.$errors" :key="error.$uid">
            {{ error.$message }}
        </div>

        <div class="input-group" v-if="$route.params.id">
            <label>
                Автор
                <select v-model="editedArticle.user_id" class="form-control input">
                    <option
                        v-for="option in allUsers"
                        :key="option.id"
                        :value="option.id"
                    >
                        {{ option.name }}
                    </option>
                </select>
            </label>
        </div>

        <div class="input-group">
            <label>
                Категория статьи
                <select v-model="editedArticle.article_category_id" class="form-control input">
                    <option
                        v-for="option in articleCategoriesLocal"
                        :key="option.id"
                        :value="option.id"
                    >
                        {{ option.name }}
                    </option>
                </select>
            </label>

            <div class="invalid-feedback" v-for="error of v$.editedArticle.article_category_id.$errors" :key="error.$uid">
                {{ error.$message }}
            </div>
        </div>


        <div class="input-group">
            Теги статьи
            <select-element>
                <template v-slot:activator="{ on }">
                    <div class="form-control select" @click="on">
                        <div
                            class="select__chip"
                            v-for="selectedTag in selectedTags"
                            :key="selectedTag.id"
                        >
                            {{selectedTag.tag}}
                        </div>
                    </div>
                </template>
                <label
                    class="select__item"
                    v-for="tag in availableTags"
                    :key="tag.id"
                >
                    <input
                        type="checkbox"
                        :value="tag.id"
                        v-model="selectedTagIds"
                    >
                    <span>{{tag.tag}}</span>
                </label>
            </select-element>
        </div>

        <div class="input-group">
            <label>
                Заголовок
                <input v-model="editedArticle.title" type="text" class="form-control input">
            </label>

            <div class="invalid-feedback" v-for="error of v$.editedArticle.title.$errors" :key="error.$uid">
                {{ error.$message }}
            </div>
        </div>


        <div class="input-group">
            <label>
                Превью
                <textarea v-model="editedArticle.preview" class="form-control textarea"></textarea>
            </label>


            <div class="invalid-feedback" v-for="error of v$.editedArticle.preview.$errors" :key="error.$uid">
                {{ error.$message }}
            </div>
        </div>
        <div class="input-group">
            Статья
            <ckeditor
                class="form__body"
                :editor="editor"
                v-model="editedArticle.body"
                :config="editorConfig">
            </ckeditor>
        </div>


        <div class="buttons">
            <btn class="button" @click="save">{{ $route.params.id ? 'Сохранить' : 'Создать' }}</btn>
            <router-link :to="{name:'article'}" class="button">
                <btn>Назад</btn>
            </router-link>
        </div>
    </form>
</template>

<script>
    import selectElement from "../../components/select-element.vue";
    import btn from '../../components/btn.vue';
    import oneImageUpload from '../../components/one-image-upload.vue';
    import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
    import UploadAdapter from '../../utils/customCKEditorUploader';
    import {mapActions, mapState} from "vuex";
    import useVuelidate from "@vuelidate/core";
    import {email, helpers, minLength, required} from "@vuelidate/validators";


    const uploader = function (editor) {
        editor.plugins.get('FileRepository').createUploadAdapter = (loader) =>
            new UploadAdapter(loader, '/');
    };

    export default {
        name: "article-form",
        components: {
            btn,
            selectElement,
            oneImageUpload,
        },
        setup () {
            return { v$: useVuelidate() }
        },
        validations () {
            return {
                editedArticle: {
                    article_category_id: {
                        required:  helpers.withMessage('Поле должно быть заполнено', required)
                    },
                    image: {
                        required:  helpers.withMessage('Нужно выбрать картинку', required),
                    },
                    title: {
                        required:  helpers.withMessage('Поле должно быть заполнено', required),
                        minLength: helpers.withMessage('Заголовок должен быть не меньше 5 символов', minLength(5))
                    },
                    preview: {
                        required:  helpers.withMessage('Поле должно быть заполнено', required),
                        minLength: helpers.withMessage('Должно быть не меньше 5 символов', minLength(5))
                    },
                    body: {
                        required:  helpers.withMessage('Поле должно быть заполнено', required),
                        minLength: helpers.withMessage('Статья должна быть не меньше 10 символов', minLength(10))
                    }
                }
            }
        },
        data() {
            return {
                editor: ClassicEditor,
                editorConfig: {extraPlugins: [uploader]},
                editedArticle: {
                    article_category_id: null,
                    tag_ids:[]
                }
            }
        },
        computed: {
            ...mapState('article', ['currentArticle', 'articleCategories']),
            ...mapState('user', ['allUsers']),
            ...mapState('tag', ['availableTags']),
            selectedTagIds: {
                get() {
                    return this.editedArticle.tag_ids
                },
                set(value) {
                    this.editedArticle.tag_ids = [...value]
                }
            },
            selectedTags () {
                return this.availableTags.filter(el => this.editedArticle.tag_ids.includes(el.id))
            },
            articleCategoriesLocal() {
                return [{
                    id: null,
                    name: 'Выберите категорию статьи'
                }].concat(this.articleCategories)
            }
        },
        async created() {
            this.loadAvailableTags();
            this.loadArticleCategories();
            this.loadAllUsers();
            if (this.$route.params.id) {
                await this.loadItem(this.$route.params.id)
                this.initEditedObject()
            }
        },
        watch: {
            async '$route.params.id' (value) {
                if (value) {
                    await this.loadItem(value)
                    this.initEditedObject()
                } else {
                    this.initNewObject();
                }
            }
        },
        methods: {
            ...mapActions('article', ['loadItem', 'createItem', 'updateItem', 'loadArticleCategories']),
            ...mapActions('user', ['loadAllUsers']),
            ...mapActions('tag', ['loadAvailableTags']),
            toggleSelectedTag(id) {
                const index =  this.editedArticle.tag_ids.indexOf(id);
                if (index === -1) {
                    this.editedArticle.tag_ids.push(id)
                } else {
                    this.editedArticle.tag_ids.splice(index, 1);
                }
            },
            initEditedObject() {
                this.editedArticle = {
                    ...this.currentArticle,
                    tag_ids: this.currentArticle.tag_ids
                        ? [...this.currentArticle.tag_ids]
                        : []
                }
            },
            initNewObject() {
                this.editedArticle = {
                    article_category_id: null,
                    tag_ids:[]
                }
            },
            async save() {
                const validated = await this.v$.editedArticle.$validate();
                if (validated) {
                    if (!this.$route.params.id) {
                        await this.createItem(this.editedArticle)
                    } else {
                        await this.updateItem(this.editedArticle)
                    }

                    this.initNewObject();
                    this.v$.$reset();
                }
            }
        }
    }
</script>

<style lang="scss" scoped>
    .form {
        max-width:1000px;
        background: inherit;
        margin-bottom:50px;
    }
    .cke_inner {
        width: 100%;
    }

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
    .buttons {
        height:35px;
        margin-top:15px;
        margin-bottom:25px;
        align-items: center;
        display: flex;
        justify-content: flex-end;
        padding: 0 25px;
    }
    .button:not(:last-child) {
        margin-right: 15px;
    }

    .input-group {
        width: 100%;
    }
    .form-control {
        width: 100%;
    }
    .select {
        display:flex;
        flex-wrap: wrap;
        min-height:36px;
        outline: #000 none medium;
        overflow: visible;
        transition: background-color 0.3s ease 0s, border-color 0.3s ease 0s;
        border: 1px solid transparent;
        border-radius: 8px;
        background-color: rgb(240, 242, 252);
        //&:hover {
        //    border-color: rgb(192, 201, 240);
        //    transition: border-color 0.3s ease 0s;
        //}
        //&:focus {
        //    background-color: white;
        //    border-color: rgb(59, 87, 208);
        //    transition: background-color 0.3s ease 0s, border-color 0.3s ease 0s;
        //}

        &__chip {
            display: inline-flex;
            align-items: center;
            line-height: 20px;
            padding: 0 12px;
            background: #e0e0e0;
            border-radius: 16px;
            height: 32px;
            flex: 0 1 auto;
            margin: 4px;
            position: relative;
            outline: none;

            &:hover:before {
                opacity: .04;
            }
            &:before {

                background-color: currentColor;
                bottom: 0;
                border-radius: inherit;
                content: "";
                left: 0;
                opacity: 0;
                position: absolute;
                pointer-events: none;
                right: 0;
                top: 0;

            }
        }
        &__item {
            height: 30px;
            display:flex;
            align-items:center;
            & input {
                margin-right: 10px;
            }
        }
    }

    .invalid-feedback {
        color: red;
    }

</style>
