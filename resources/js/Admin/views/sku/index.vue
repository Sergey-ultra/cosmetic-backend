<template>
    <data-table
        :isLoading="isLoading"
        :optionsItemsPerPage="optionsItemsPerPage"
        :total="total"
        :items="skus"
        :headers="headers"
        :availableOptions="availableOptions"
        v-model:options="options"
        v-model:filter="filter"
        @reloadTable="reloadSkus"
    >
        <template v-slot:add>
            <btn @click="!creationDisabled ? showForm() : ''" :disabled="creationDisabled">Добавить</btn>
        </template>

        <template v-slot:images="product">
            <div class="flex">
                <img  v-for="image in product.item.images"  :key="image" class="image" :src="image" :alt="image">
            </div>
        </template>

        <template v-slot:action="product">
            <div class="action" @click="showForm(product.item.id)">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     class="bi bi-pencil-fill" viewBox="0 0 16 16">
                    <path
                        d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                </svg>
            </div>
            <div class="action" @click="showDeleteForm(product.item)">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     class="bi bi-trash-fill" viewBox="0 0 16 16">
                    <path
                        d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z"/>
                </svg>
            </div>
        </template>
    </data-table>

    <sku-form
        :selectedSkuId="selectedSkuId"
        v-if="isShowForm"
        v-model:isShowForm="isShowForm"
    />

    <delete-form
            :selectedName="selectedProductName"
            v-if="isShowDeleteForm"
            v-model:isShowDeleteForm="isShowDeleteForm"
            @delete="deleteSku"
    />

</template>

<script>
    import dataTable from '../../components/data-table.vue'
    import btn from "../../components/btn.vue"
    import skuForm from './sku-form.vue'
    import deleteForm from '../../components/delete-form.vue'
    import {mapActions, mapGetters, mapMutations, mapState} from "vuex";

    export default {
        name: "skus",
        components: {
            dataTable,
            btn,
            skuForm,
            deleteForm
        },
        data() {
            return {
                headers: [
                    {title: 'id', value: 'id', width: '5%'},
                    {title: 'Категория', value: 'category', width: '5%', filter: {type: 'select'}},
                    {title: 'Бренд', value: 'brand', width: '10%', filter: {type: 'input'}},
                    {title: 'Изображение', value: 'images', width: '15%', sort: false},
                    {title: 'Имя', value: 'name', width: '20%', filter: {type: 'input'}},
                    {title: 'Код', value: 'code', width: '25%', filter: {type: 'input'}},
                    {title: 'Объем', value: 'volume', width: '5%', filter: {type: 'input'}},
                    {title: 'Дата', value: 'created_at', width: '5%'},
                    {title: 'Действия', value: 'action', width: '10%'}
                ],
                optionsItemsPerPage: [5, 10, 20, 50, 100],
                isShowForm: false,
                isShowDeleteForm: false,
                selectedSkuId: null,
                selectedProductName: null,
                creationDisabled: true
            }
        },
        computed: {
            ...mapState('sku', ['tableOptions', 'filterOptions', 'isLoading', 'skus', 'total']),
            ...mapGetters('category', ['availableCategoryNames']),
            availableOptions() {
                return {
                    category: this.availableCategoryNames
                }
            },
            filter: {
                get() {
                    return this.filterOptions
                },
                set(value) {
                    this.setFilterOptions(value)
                    this.loadSkus()
                }
            },
            options:{
                get() {
                    return this.tableOptions
                },
                set(value) {
                    this.setTableOptions(value)
                    this.loadSkus()
                }
            }
        },
        created() {
            if (!this.skus.length) {
                this.loadSkus()
            }

            if (!this.availableCategoryNames.length) {
                this.loadAllCategories()
            }
        },
        watch: {

        },
        methods: {
            ...mapActions('sku', ['reloadSkus', 'loadSkus', 'deleteItem', 'loadAvailableCategories']),
            ...mapActions('category', ['loadAllCategories']),
            ...mapMutations('sku', ['setTableOptions', 'setFilterOptions']),
            showForm(id = null) {
                this.isShowForm = true
                this.selectedSkuId = id
            },
            showDeleteForm(item) {
                this.isShowDeleteForm = true
                this.selectedSkuId = item.id
                this.selectedProductName = item.name
            },
            deleteSku(){
                this.deleteItem(this.selectedSkuId)
                this.isShowDeleteForm = false
            }
        }
    }
</script>

<style scoped lang="scss">
    a {
        color:#fff;
        text-decoration: none;
    }
    .action {
        cursor: pointer;
        &:not(:last-child) {
            margin-right: 20px;
        }
    }
    .image {
        height: 32px;
        width: 32px;
        display: block;
        margin : 5px;
        background-size: 32px 32px;
        border: 0;
        border-radius: 50%;
        cursor: pointer;
        &:hover {
            transition: all 0.2s ease;
            transform: scale(1.2);
        }
    }
    .flex {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
    }
</style>
