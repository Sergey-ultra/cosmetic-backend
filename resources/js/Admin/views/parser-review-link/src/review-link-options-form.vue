<template>
    <form class="form__block" @input="setFormChangingToTrue">
        <div class="form__element">
            <div for="target__catalog">Первая страница Каталога</div>
            <input class="target__url" type="text" name="target__catalog"
                   v-model="options.categoryUrl"
                   :placeholder="options.categoryUrl">
        </div>

        <div class="form__element">
            <div for="target__catalog">Query-параметр пагинации</div>
            <input class="target__url" type="text" name="target__catalog" v-model="options.paginationQuery">
        </div>

        <div class="form__element">
            <div>тег ссылки товарного предложения</div>
            <input class="tagName" type="text" v-model="options.link">
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
            <div>Начальная страница</div>
            <input class="tagName" type="text" v-model="options.startPageNumber">
        </div>
        <div class="form__element">
            <div>Конечная страница</div>
            <input class="tagName" type="text" v-model="options.endPageNumber">
        </div>

        <div class="form__element">
            <buttonComponent
                :disabled="!(isFormChanging && (categoryId !== null || categoryId !== 'null'))"
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
                link: "",
                relatedLink: true,
                nextPage:"",
                relatedPageUrl: true,
                startPageNumber: 0,
                endPageNumber: null,
                paginationQuery: '',
            }
        }
    },
    props: {
        categoryId: {
            default: null
        }
    },
    computed: {
        ...mapState('reviewParser', ['linkOptions']),
    },
    watch: {
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
        ...mapActions('reviewParser', ['loadLinkOptions', 'saveLinkOptions']),
        setFormChangingToTrue() {
            this.isFormChanging = true
        },
        async saveOptions() {
            this.isFormChanging = false
            this.options.startPageNumber = Number(this.options.startPageNumber)
            this.options.endPageNumber = this.options.endPageNumber === null || this.options.endPageNumber === ''
                ? null
                : Number(this.options.endPageNumber)
            this.saveLinkOptions({
                category_id: this.categoryId,
                options: this.options
            })
        },
        async loadOptions() {
            this.isFormChanging = false
            if (this.categoryId && this.categoryId !== 'null') {
                this.loadLinkOptions({category_id: this.categoryId})
            } else {
                this.options = {
                    categoryUrl: "",
                    link: "",
                    relatedLink: true,
                    nextPage:"",
                    relatedPageUrl: true,
                    startPageNumber: 0,
                    endPageNumber: null,
                    paginationQuery: '',
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
