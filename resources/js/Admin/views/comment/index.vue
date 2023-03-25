<template>
    <data-table
            :isLoading="isLoading"
            :optionsItemsPerPage="optionsItemsPerPage"
            :total="total"
            :items="comments"
            :headers="headers"
            v-model:options="options"
            v-model:filter="filter"
            @reloadTable="reloadComments"
    >
        <template v-slot:add>
            <buttonComponent :size="'small'" :color="'blue'" @click="showForm(null)">Добавить</buttonComponent>
        </template>


        <template v-slot:action="comment">
            <div class="action">
                <drop-menu :items="['publish', 'moderated', 'rejected', 'deleted']">
                    <template v-slot:publish v-if="comment.item.status !== 'published'">
                        <div class="dropdown__inner">
                            <button
                                    class="dropdown__value"
                                    @click="setStatus({ id: comment.item.id, status: 'published' })"
                            >
                                <span>Опубликовать</span>
                            </button>
                        </div>
                    </template>
                    <template v-slot:moderated v-if="comment.item.status !== 'moderated'">
                        <div class="dropdown__inner">
                            <button
                                    class="dropdown__value"
                                    @click="setStatus({ id: comment.item.id, status: 'moderated' })"
                            >
                                <span>Поставить на модерацию</span>
                            </button>
                        </div>
                    </template>
                    <template v-slot:rejected  v-if="comment.item.status !== 'rejected'">
                        <div class="dropdown__inner">
                            <button
                                    class="dropdown__value"
                                    @click="setStatus({ id: comment.item.id, status: 'rejected' })"
                            >
                                <span>Отклонить</span>
                            </button>
                        </div>
                    </template>
                    <template v-slot:deleted  v-if="comment.item.status !== 'deleted'">
                        <div class="dropdown__inner">
                            <button
                                    class="dropdown__value"
                                    @click="setStatus({ id: comment.item.id, status: 'deleted' })"
                            >
                                <span>Удалить</span>
                            </button>
                        </div>
                    </template>
                </drop-menu>
            </div>
            <buttonComponent
                class="action"
                @click="showForm(comment.item.id)"
                :color="'green'"
                :icon="true"
                :outline="true"
                :round="true"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     class="bi bi-pencil-fill" viewBox="0 0 16 16">
                    <path
                            d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                </svg>
            </buttonComponent>
            <buttonComponent
                class="action"
                @click="showDeleteForm(comment.item)"
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
            :selectedName="selectedName"
            v-if="isShowDeleteForm"
            v-model:isShowDeleteForm="isShowDeleteForm"
            @delete="deleteComment"
    />
</template>

<script>
    import dataTable from '../../components/data-table.vue'
    import deleteForm from '../../components/delete-form.vue'
    import dropMenu from '../../components/drop-menu.vue'
    import {mapActions, mapMutations, mapState} from "vuex";
    import buttonComponent from "../../components/button-component.vue";

    export default {
        name: "comments",
        components: {
            dataTable,
            buttonComponent,
            deleteForm,
            dropMenu
        },
        data: () => ({
            headers: [
                {title: 'id', value: 'id', width: '5%'},
                {title: 'Имя пользователя', value: 'user_name', width: '15%'},
                {title: 'Комментарий', value: 'comment', width: '20%'},
                {title: 'Review_id', value: 'review_id', width: '5%'},
                {title: 'Reply_id', value: 'reply_id', width: '5%'},
                {title: 'Статус', value: 'status', width: '15%'},
                {title: 'Дата', value: 'created_at', width: '25%'},
                {title: 'Действия', value: 'action', width: '10%'}
            ],
            optionsItemsPerPage: [5, 10, 20, 50, 100],
            isShowForm: false,
            selectedCommentId: null,
            isShowDeleteForm:false,
            selectedName: null,
        }),
        computed:{
            ...mapState('comment', ['tableOptions', 'filterOptions', 'isLoading', 'comments', 'total']),
            filter: {
                get() {
                    return this.filterOptions
                },
                set(value) {
                    this.setFilterOptions(value)
                    this.loadComments()
                }
            },
            options: {
                get() {
                    return this.tableOptions
                },
                set(value) {
                    this.setTableOptions(value)
                    this.loadComments()
                }
            }
        },
        created() {
            if (!this.comments.length) {
                this.loadComments()
            }
        },
        watch: {

        },
        methods: {
            ...mapActions('comment', ['reloadComments', 'loadComments', 'deleteItem', 'setStatus']),
            ...mapMutations('comment', ['setTableOptions', 'setFilterOptions']),
            showForm(id) {
                this.isShowForm = true
                this.selectedCommentId = id
            },
            showDeleteForm(item) {
                this.isShowDeleteForm = true
                this.selectedCommentId = item.id
                this.selectedName = item.name
            },
            deleteComment() {
                this.deleteItem(this.selectedCommentId)
                this.isShowDeleteForm = false
            }
        }
    }
</script>

<style scoped lang="scss">
    @import './resources/css/admin/table.scss';
</style>
