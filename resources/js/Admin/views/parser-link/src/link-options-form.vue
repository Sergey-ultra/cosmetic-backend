<template>
    <form class="form__block" @input="setFormChangingToTrue">
        <div class="form__element">
            <div for="target__catalog">Первая страница Каталога</div>
            <input class="target__url" type="text" name="target__catalog"
                   v-model="options.categoryUrl"
                   :placeholder="options.categoryUrl"
            >
        </div>

        <div class="form__element">
            <div>тег ссылки товарного предложения</div>
            <input class="tagName" type="text" v-model="options.productLink">
            <div>
                <label>
                    <input type="checkbox" v-model="options.relatedLink">
                    <span>относительная ссылка</span>
                </label>
            </div>
        </div>

        <div class="form__element">
            <div>тег кнопки следующей страницы</div>
            <input class="tagName" type="text" v-model="options.nextPage">
            <div>
                <label>
                    <input type="checkbox" v-model="options.relatedPageUrl">
                    <span>относительная ссылка</span>
                </label>
            </div>
        </div>

        <div class="form__element">
                <buttonComponent
                    :disabled="!(isFormChanging && (storeId !== null || storeId !== 'null'))"
                    @click="saveOptions"
                >
                    Сохранить настройки
                </buttonComponent>
            </div>
    </form>
</template>

<script>
import {mapActions, mapState} from "vuex";
import buttonComponent from "../../../components/button-component.vue"

export default {
    name: "link-options-form",
    components: {
        buttonComponent
    },
    data() {
        return {
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
    props: {
        storeId: {
            default: null
        },
        categoryId: {
            default: null
        }
    },
    computed: {
        ...mapState('linkOptions', ['linkOptions']),
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
    created() {
        this.loadOptions()
    },
    methods: {
        ...mapActions('linkOptions', ['loadLinkOptions', 'saveLinkOptions']),
        setFormChangingToTrue() {
            this.isFormChanging = true
        },
        async saveOptions() {
            this.isFormChanging = false
            this.saveLinkOptions({
                store_id: this.storeId,
                category_id: this.categoryId,
                options: this.options
            })
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
    }
}
</script>

<style scoped lang="scss">
@import '../../../../../css/admin/form';
    .target__url {
        width: 900px;
    }
</style>
