<template>
    <data-table
        :isLoading="isLoadingQuestions"
        :optionsItemsPerPage="optionsItemsPerPage"
        :total="total"
        :items="questionWithPagination"
        :headers="headers"
        :availableOptions="availableOptions"
        v-model:options="options"
        v-model:filter="filter"
        @reloadTable="reloadQuestions"
    >

        <template v-slot:action="question">
            <div class="action">
                <drop-menu :items="['publish', 'moderated', 'rejected', 'deleted']">
                    <template v-slot:publish
                              v-if="question.item.status !== 'published'"
                    >
                        <div class="dropdown__inner">
                            <button
                                class="dropdown__value"
                                @click="setStatus({ id: question.item.id, status: 'published' })"
                            >
                                <span>Опубликовать</span>
                            </button>
                        </div>
                    </template>
                    <template v-slot:moderated v-if="question.item.status !== 'moderated'">
                        <div class="dropdown__inner">
                            <button
                                class="dropdown__value"
                                @click="setStatus({ id: question.item.id, status: 'moderated' })"
                            >
                                <span>Поставить на модерацию</span>
                            </button>
                        </div>
                    </template>
                    <template v-slot:rejected  v-if="question.item.status !== 'rejected'">
                        <div class="dropdown__inner">
                            <button
                                class="dropdown__value"
                                @click="setStatus({ id: question.item.id, status: 'rejected' })"
                            >
                                <span>Отклонить</span>
                            </button>
                        </div>
                    </template>
                    <template v-slot:deleted
                              v-if="question.item.status !== 'deleted'">
                        <div class="dropdown__inner">
                            <button
                                class="dropdown__value"
                                @click="setStatus({ id: question.item.id, status: 'deleted' })"
                            >
                                <span>Удалить</span>
                            </button>
                        </div>
                    </template>
                </drop-menu>
            </div>
        </template>
    </data-table>

</template>

<script>
import dataTable from '../../components/data-table.vue'
import btn from '../../components/btn.vue'
import dropMenu from "../../components/drop-menu.vue";
import {mapActions, mapMutations, mapState} from "vuex";

export default {
    name: "brands",
    components: {
        dataTable,
        btn,
        dropMenu
    },
    data: () => ({
        headers: [
            {title: 'id', value: 'id', width: '5%'},
            {title: 'IP адрес', value: 'ip_address', width: '10%'},
            {title: 'user_id', value: 'user_id', width: '5%'},
            {title: 'user_name', value: 'user_name', width: '10%'},
            {title: 'sku_id', value: 'sku_id', width: '5%'},
            {title: 'Вопрос', value: 'body', width: '35%'},
            {title: 'Статус', value: 'status', width: '10%'},
            {title: 'reply_id', value: 'reply_id', width: '5%'},
            {title: 'Дата', value: 'created_at', width: '5%'},
            {title: 'Действия', value: 'action', width: '10%'}
        ],
        optionsItemsPerPage: [5, 10, 20, 50, 100],
    }),
    computed:{
        ...mapState('question', ['tableOptions', 'filterOptions', 'isLoadingQuestions', 'questionWithPagination', 'total']),
        availableOptions() {
            return {

            }
        },
        filter: {
            get() {
                return this.filterOptions
            },
            set(value) {
                this.setFilterOptions(value)
                this.loadBrands()
            }
        },
        options: {
            get() {
                return this.tableOptions
            },
            set(value) {
                this.setTableOptions(value)
                this.loadQuestionsWithPagination()
            }
        }
    },
    created() {
        if (!this.questionWithPagination.length) {
            this.loadQuestionsWithPagination()
        }
    },
    watch: {

    },
    methods: {
        ...mapActions('question', ['reloadQuestions', 'loadQuestionsWithPagination', 'setStatus']),
        ...mapMutations('question', ['setTableOptions', 'setFilterOptions']),
    }
}
</script>

<style scoped lang="scss">
@import './resources/css/admin/table.scss';
</style>
