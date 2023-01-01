<template>
    <data-table
            :items="trackingsWithPagination"
            :headers="headers"
            :isLoading="isLoading"
            :optionsItemsPerPage="optionsItemsPerPage"
            :total="total"
            v-model:options="options"
            v-model:filter="filter"
    >
    </data-table>

    <delete-form
            :selectedName="selectedName"
            v-if="isShowDeleteForm"
            v-model:isShowDeleteForm="isShowDeleteForm"
            @delete="deleteTracking"
    />
</template>

<script>
    import dataTable from '../../components/data-table.vue'
    import modal from '../../components/modal/modal.vue'
    import btn from '../../components/btn.vue'
    import deleteForm from '../../components/delete-form.vue'
    import {mapActions, mapMutations, mapState} from "vuex";

    export default {
        name: "trackings",
        components:{
            dataTable,
            modal,
            btn,
            deleteForm
        },
        data() {
            return {
                headers: [
                    {title: 'id', value: 'id', width: '5%'},
                    {title: 'email', value: 'email', width: '15%'},
                    {title: 'Название', value: 'name', width: '27%'},
                    {title: 'Объем', value: 'volume', width: '15%'},
                    {title: 'Цена', value: 'price', width: '15%'},
                    {title: 'Дата', value: 'created_at', width: '15%'},
                    {title: 'Действия', value: 'action', width: '8%'}
                ],
                optionsItemsPerPage: [5, 10, 20, 50, 100],
                isShowForm: false,
                selectedTrackingId: null,
                isShowDeleteForm: false,
                selectedName: null,
            }
        },
        computed:{
            ...mapState('dynamics', ['tableOptions', 'filterOptions', 'isLoading', 'trackingsWithPagination', 'total']),
            filter: {
                get() {
                    return this.filterOptions
                },
                set(value) {
                    this.setFilterOptions(value)
                    this.loadTrackingsWithPagination()
                }
            },
            options: {
                get() {
                    return this.tableOptions
                },
                set(value){
                    this.setTableOptions(value)
                    this.loadTrackingsWithPagination()
                }
            },
        },
        created() {
            if (!this.trackingsWithPagination.length) {
                this.loadTrackingsWithPagination()
            }
        },
        watch: {

        },
        methods: {
            ...mapActions('dynamics', ['loadTrackingsWithPagination']),
            ...mapMutations('dynamics', ['setTableOptions', 'setFilterOptions']),
        }
    }
</script>

<style scoped lang="scss">
    .action {
        cursor: pointer;
        &:not(:last-child) {
            margin-right: 20px;
        }
    }
</style>
