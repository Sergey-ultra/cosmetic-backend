<template>
    <data-table
            :isLoading="isLoading"
            :optionsItemsPerPage="optionsItemsPerPage"
            :total="total"
            :items="articlesWithPagination"
            :headers="headers"
            :availableOptions="availableOptions"
            v-model:options="options"
            v-model:filter="filter"
            @reloadTable="reloadArticles"
    >
        <template v-slot:add>
            <router-link :to="{ name: 'article-create' }">
                <buttonComponent :size="'small'" :color="'blue'">Добавить</buttonComponent>
            </router-link>
        </template>
        <template v-slot:action="article">
            <div class="action">
                <drop-menu :items="['publish']">
                    <template v-slot:publish>
                        <div class="dropdown__inner">
                            <button
                                    class="dropdown__value"
                                    v-if="article.item.status === 'moderated'"
                                    @click="publishItem(article.item.id)"
                            >
                                <span>Опубликовать</span>
                            </button>
                            <button
                                    class="dropdown__value"
                                    v-if="article.item.status === 'published'"
                                    @click="withdrawFromPublication(article.item.id)"
                            >
                                <span>Снять с публикации</span>
                            </button>
                        </div>
                    </template>
                </drop-menu>
            </div>
            <buttonComponent
                class="action"
                :color="'green'"
                :icon="true"
                :outline="true"
                :round="true"
            >
                <router-link :to="{name: 'article-edit', params: { id: article.item.id}}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                         class="bi bi-pencil-fill" viewBox="0 0 16 16">
                        <path
                                d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                    </svg>
                </router-link>
            </buttonComponent>
            <buttonComponent
                class="action"
                @click="showDeleteForm(article.item)"
                :color="'red'"
                :icon="true"
                :outline="true"
                :round="true"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     class="bi bi-trash-fill" viewBox="0 0 16 16">
                    <path
                            d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z"/>
                </svg>
            </buttonComponent>
        </template>
    </data-table>

    <delete-form
            :selectedId="selectedArticleId"
            :selectedName="selectedName"
            v-if="isShowDeleteForm"
            v-model:isShowDeleteForm="isShowDeleteForm"
            @delete="deleteArticle"
    />

</template>

<script>
    import dataTable from '../../components/data-table.vue'
    import deleteForm from '../../components/delete-form.vue'
    import dropMenu from '../../components/drop-menu.vue'
    import {mapActions, mapGetters, mapMutations, mapState} from "vuex";
    import buttonComponent from "../../components/button-component.vue";
    export default {
        name: "article",
        components: {
            dataTable,
            buttonComponent,
            deleteForm,
            dropMenu
        },
        data() {
            return {
                headers: [
                    {title: 'id', value: 'id', width: '5%'},
                    {title: 'Заголовок', value: 'title', width: '20%', filter: { type: 'input' } },
                    {title: 'Превью', value: 'preview', width: '15%', filter: { type: 'input' }},
                    {title: 'Категория', value: 'category_name', width: '10%', filter: { type: 'select' }},
                    {title: 'Создатель', value: 'user', width: '10%', filter: { type: 'input' }},
                    {title: 'Статус', value: 'status', width: '10%'},
                    {title: 'Дата создания', value: 'created_at', width: '5%'},
                    {title: 'Действия', value: 'action', width: '25%'}
                ],
                optionsItemsPerPage: [5, 10, 20, 50, 100],
                isShowDeleteForm: false,
                selectedArticleId: null,
                selectedName: null,
            }
        },
        computed:{
            ...mapState('article', ['tableOptions', 'filterOptions', 'isLoading', 'articlesWithPagination', 'total', 'articleCategories']),
            availableOptions() {
                return {
                    category_name: this.articleCategories.map(el => ({ title: el.name, value: el.name})),
                }
            },
            filter: {
                get() {
                    return this.filterOptions
                },
                set(value) {
                    this.setFilterOptions(value)
                    this.loadArticlesWithPagination()
                }
            },
            options: {
                get(){
                    return this.tableOptions
                },
                set(value) {
                    this.setTableOptions(value)
                    this.loadArticlesWithPagination()
                }
            }
        },
        created() {
            if (!this.articlesWithPagination.length) {
                this.loadArticlesWithPagination();
            }
            if (!this.articleCategories.length) {
                this.loadArticleCategories();
            }
        },
        methods: {
            ...mapActions('article', ['loadArticlesWithPagination', 'reloadArticles', 'loadArticleCategories',
                'deleteItem', 'publishItem', 'withdrawFromPublication']),
            ...mapMutations('article', ['setTableOptions', 'setFilterOptions']),
            showDeleteForm(item) {
                this.isShowDeleteForm = true
                this.selectedArticleId = item.id
                this.selectedName = item.title
            },
            deleteArticle(){
                this.deleteItem(this.selectedArticleId)
                this.isShowDeleteForm = false
            }
        }
    }
</script>

<style scoped lang="scss">
@import './resources/css/admin/table.scss';
</style>
