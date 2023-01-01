<template>
    <div class="form__element">
        <div>Выберите магазин</div>
        <select v-model="storeId">
            <option value="null">Выберите магазин</option>
            <option
                    v-for="store in allStores"
                    :key="store.id"
                    :value="store.id"
            >
                {{ store.id }}    {{ store.name }}
            </option>
        </select>
    </div>
    <div class="form__element">
        <div>Выберите категорию</div>
        <select v-model="categoryId">
            <option value="null">Выберите категорию</option>
            <option
                    v-for="category in allCategories"
                    :key="category.id"
                    :value="category.id"
            >
                {{ category.id }}    {{ category.name }}
            </option>
        </select>
    </div>
    <form class="form__block" @input="setFormChangingToTrue">
        <div>
            <div class="form__element">
                <div for="target__catalog">Относительный адрес Каталога</div>
                <input class="target__url" type="text" name="target__catalog"
                       v-model="options.categoryUrl"
                       :placeholder="categoryUrl"
                >
            </div>
            <div class="form__element">
                <div>тег ссылки товарного предложения</div>
                <input class="tagName" type="text" v-model="options.productLink">
                <div>относительная ссылка</div>
                <input  type="checkbox" v-model="options.relatedLink">
            </div>
            <div class="form__element">
                <div>тег кнопки следующей страницы</div>
                <input class="tagName" type="text" v-model="options.nextPage">
                <div>относительная ссылка</div>
                <input  type="checkbox" v-model="options.relatedPageUrl">
            </div>
            <div class="form__element">
                <button
                        type="button"
                        class="btn"
                        :disabled="!(isFormChanging && storeId !== null)"
                        @click="saveOptions"
                >
                    Сохранить настройки
                </button>
            </div>
        </div>
    </form>


    <div class="parser__settings">
        <label>
            Загрузить в базу данных
            <input type="checkbox" v-model="isLoadToDb">
        </label>
    </div>
    <div class="form__element">
        <button
                type="button"
                class="btn"
                :disabled="storeId === null || categoryId === null"
                @click="parse"
        >
            Спарсить ссылки
        </button>
    </div>

    {{ message }}

</template>

<script>
    import {mapActions, mapState} from "vuex";

    export default {
        name: "link-parser",
        data() {
            return {
                isLoadToDb: false,
                storeId: null,
                categoryId: null,
                isFormChanging:false,
                options: {
                    categoryUrl: "",
                    productLink: "",
                    relatedLink: true,
                    nextPage:"",
                    relatedPageUrl: true
                }
            }
        },
        computed: {
            ...mapState('store',['allStores']),
            ...mapState('category',['allCategories']),
            ...mapState('linkOptions',['linkOptions']),
            ...mapState('linkParser',['message']),
        },
        async created() {
            this.loadAllStores()
            this.loadAllCategories()
        },
        watch: {
            storeId() {
                this.loadOptions()
            },
            categoryId() {
                this.loadOptions()
            },
            linkOptions(value) {
                this.options = { ...value }
            }
        },
        methods:{
            ...mapActions('store', ['loadAllStores']),
            ...mapActions('category', ['loadAllCategories']),
            ...mapActions('linkOptions', ['loadLinkOptions', 'saveLinkOptions']),
            ...mapActions('linkParser', ['parseLinks']),
            setFormChangingToTrue() {
                this.isFormChanging = true
            },
            async loadOptions() {
                this.isFormChanging = false
                if (this.storeId && this.storeId !== 'null' && this.categoryId && this.categoryId !== 'null') {
                    this.loadLinkOptions({
                        store_id: this.storeId,
                        category_id: this.categoryId
                    })
                } else {
                    this.options = {
                        categoryUrl: "",
                            productLink: "",
                            relatedLink: true,
                            nextPage:"",
                            relatedPageUrl: true
                    }
                }
            },
            async saveOptions() {
                this.isFormChanging = false
                this.saveLinkOptions({
                    store_id: this.storeId,
                    category_id: this.categoryId,
                    options: this.options
                })
            },
            async parse() {
                this.parseLinks({
                    category_id: this.categoryId,
                    store_id: this.storeId,
                    isLoadToDb: this.isLoadToDb,
                })
            }
        }
    }
</script>

<style lang="scss" scoped>
    button {
        min-width: 28px;
        padding: 0 20px;
        display: flex;
        justify-content: center;
        position: relative;
        align-items: center;
        border-radius: 4px;
        color: #fff;
        height: 35px;
        background: rgb(24, 103, 192) none repeat scroll 0% 0%;
        border: 1px solid rgb(24, 103, 192);

        &:hover::before {
            opacity: .08;
        }
        &[disabled] {
            border: none;
            background-color: rgba(0,0,0,.12);
            color: rgba(0,0,0,.26);
        }
    }

    .form__block {
        background-color: rgba(0, 0, 0, 0.08);
        border-radius:5px;
        border: 1px solid rgba(0,0,0,0.55);
        display:flex;
        align-items:flex-end;
        margin:29px 0;
        box-shadow: 0px 1px 4px rgba(0,0,0,0.15);
        padding:20px;
    }
    .form__group {
        display: flex;
        justify-content: space-between;
    }
    .target__url {
        width:900px;
    }
    .tagName {
        width:900px;
    }
</style>