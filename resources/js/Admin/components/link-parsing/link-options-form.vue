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
                <button
                    type="button"
                    class="btn"
                    :disabled="!(isFormChanging && storeId !== null)"
                    @click="saveOptions"
                >
                    Сохранить настройки
                </button>
            </div>
    </form>
</template>

<script>
import {mapActions, mapState} from "vuex";

export default {
    name: "link-options-form",
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
            background-color: rgba(0, 0, 0, .12);
            color: rgba(0, 0, 0, .26);
        }

        &-settings {
            margin-top: 20px;
            margin-left: auto;
        }
    }
    .form {
        &__group {
            display: flex;
            justify-content: space-between;
        }
        &__element {
            margin: 15px 0;
        }
        &__block {
            background-color: rgba(0, 0, 0, 0.08);
            border-radius:5px;
            border: 1px solid rgba(0,0,0,0.55);
            align-items:flex-end;
            margin:29px 0;
            box-shadow: 0px 1px 4px rgba(0,0,0,0.15);
            padding:20px;
        }
        &__icon {
            fill: #fff;
            margin-right: 10px;
            width: 20px;
            height: 20px;
        }
    }

    .target__url {
        width: 900px;
    }

</style>
