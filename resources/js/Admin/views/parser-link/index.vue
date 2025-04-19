<template>
    <form>
        <div class="form__group">
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
        </div>

        <div class="form__group">
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
        </div>

        <div class="form__group">
            <buttonComponent
                :size="'small'"
                class="button-settings"
                @click="toggleIsShowLinkOptions"
            >
                <svg class="form__icon" viewBox="0 0 24 24"><path d="M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8zm0 6a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"></path><path d="m22.215 7.759-1.427-2.483a1.398 1.398 0 0 0-1.755-.591l-2.22.93-1.69-.97-.307-2.422A1.403 1.403 0 0 0 13.426 1h-2.853a1.403 1.403 0 0 0-1.39 1.224L8.88 4.622l-1.72.982-2.191-.92a1.4 1.4 0 0 0-1.756.593L1.787 7.756a1.403 1.403 0 0 0 .364 1.814l1.855 1.41.003 2.038-1.859 1.413a1.402 1.402 0 0 0-.365 1.81l1.427 2.482a1.398 1.398 0 0 0 1.754.592l2.22-.93 1.69.97.308 2.421A1.404 1.404 0 0 0 10.574 23h2.853a1.403 1.403 0 0 0 1.39-1.224l.304-2.398 1.72-.982 2.192.92a1.402 1.402 0 0 0 1.755-.593l1.425-2.479a1.401 1.401 0 0 0-.365-1.814l-1.854-1.41-.002-2.038L21.85 9.57a1.402 1.402 0 0 0 .365-1.81zm-4.226 2.233.007 4.023 2.222 1.687-.9 1.565-2.613-1.097-3.443 1.966L12.898 21h-1.796l-.367-2.886-3.412-1.956-2.641 1.109-.9-1.565 2.229-1.694-.007-4.02-2.222-1.69.9-1.565L7.294 7.83l3.444-1.966L11.102 3h1.796l.367 2.886 3.412 1.956 2.641-1.109.9 1.565-2.229 1.694z"></path></svg>
                <span>{{ isShowLinkOptions ? 'Скрыть настройки' : 'Показать настройки'}}</span>
            </buttonComponent>
        </div>
    </form>

    <link-options-form
        v-if="isShowLinkOptions"
        :storeId="storeId"
        :categoryId="categoryId"
    />



    <div class="settings">
        <div class="setting">
            <label>
                <input type="checkbox" v-model="isLoadToDb">
                Загрузить в базу данных
            </label>
        </div>
        <div class="setting">
            <label>
                <input type="checkbox" v-model="isOpenPreviewAfterParsingLocal">
                Показывать результаты в modal
            </label>
        </div>
    </div>
    <div class="form__element">
        <buttonComponent
            :disabled="storeId === 'null' || categoryId === 'null'"
            :isLoading="isParsing"
            @click="parse"
        >
            Спарсить ссылки
        </buttonComponent>
    </div>

    <preview-links-modal
        v-if="isShowPreviewLocal"
        v-model:isShowForm="isShowPreviewLocal"
    />

</template>

<script>
    import {mapActions, mapMutations, mapState} from "vuex";
    import previewLinksModal from "./src/preview-links-modal.vue";
    import linkOptionsForm from "./src/link-options-form.vue";
    import buttonComponent from "../../components/button-component.vue"

    export default {
        name: "link-parser",
        components: {
            buttonComponent,
            previewLinksModal,
            linkOptionsForm
        },
        data() {
            return {
                isLoadToDb: false,
                storeId: 'null',
                categoryId: 'null',
                isShowLinkOptions: false,
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
            ...mapState('linkParser',['isParsing', 'isShowPreview', 'isOpenPreviewAfterParsing']),
            isShowPreviewLocal: {
                get() {
                    return this.isShowPreview;
                },
                set(value) {
                    this.setIsShowPreview(value);
                }
            },
            isOpenPreviewAfterParsingLocal: {
                get() {
                    return this.isOpenPreviewAfterParsing
                },
                set(value) {
                    this.setIsOpenPreviewAfterParsing(value)
                }
            }
        },
        async created() {
            this.loadAllStores()
            this.loadAllCategories()
        },
        methods:{
            ...mapActions('store', ['loadAllStores']),
            ...mapActions('category', ['loadAllCategories']),
            ...mapActions('linkParser', ['parseLinks']),
            ...mapMutations('linkParser', ['setIsShowPreview', 'setIsOpenPreviewAfterParsing']),
            async parse() {
                await this.parseLinks({
                    category_id: this.categoryId,
                    store_id: this.storeId,
                    isLoadToDb: this.isLoadToDb,
                })
            },
            toggleIsShowLinkOptions() {
                this.isShowLinkOptions = !this.isShowLinkOptions
            }
        }
    }
</script>

<style lang="scss" scoped>
@import '../../../../css/admin/form';
    .button-settings {
        margin-top: 20px;
        margin-left: auto;
    }
    .setting {
        margin: 12px 0;
    }
</style>
