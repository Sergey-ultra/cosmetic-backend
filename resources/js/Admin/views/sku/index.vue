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
            <buttonComponent
                :size="'small'"
                :color="'blue'"
                :disabled="creationDisabled"
                @click="!creationDisabled ? showForm() : ''"
            >
                Добавить
            </buttonComponent>
        </template>

        <template v-slot:images="{ item }">
            <div class="image__wrapper">
                <img  v-for="image in item.images"  :key="image" class="image" :src="image" :alt="image">
            </div>
        </template>

        <template v-slot:name="{ item }">
          <router-link :to="`/product/${item.url}`">
              {{ item.name }}
          </router-link>
        </template>

        <template v-slot:is_ingredients_exist="{ item }">
            {{ item.is_ingredients_exist === 1 ? 'Да' : 'Нет' }}
        </template>

        <template v-slot:status="{ item }">
            <span :class="{'status': item.status === 'moderated'}">
                {{ item.status }}
            </span>
        </template>

        <template v-slot:action="{ item }">
            <buttonComponent
                class="action"
                @click="showForm(item.id)"
                :color="'green'"
                :icon="true"
                :outline="true"
                :round="true"
            >
                <svg width="16" height="16" fill="currentColor"
                     class="bi bi-pencil-fill" viewBox="0 0 16 16">
                    <path
                        d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                </svg>
            </buttonComponent>
            <buttonComponent
                class="action"
                @click="showDeleteForm(item)"
                :color="'red'"
                :icon="true"
                :outline="true"
                :round="true"
            >
                <svg width="16" height="16" fill="currentColor"
                     class="bi bi-trash-fill" viewBox="0 0 16 16">
                    <path
                        d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z"/>
                </svg>
            </buttonComponent>
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
    import skuForm from './sku-form.vue'
    import deleteForm from '../../components/delete-form.vue'
    import {mapActions, mapGetters, mapMutations, mapState} from "vuex";
    import buttonComponent from "../../components/button-component.vue";

    export default {
        name: "skus",
        components: {
            dataTable,
            buttonComponent,
            skuForm,
            deleteForm
        },
        data() {
            return {
                headers: [
                    {title: 'id', value: 'id', width: '5%'},
                    {title: 'Категория', value: 'category', width: '5%', filter: {type: 'select'}},
                    {title: 'Бренд', value: 'brand', width: '7%', filter: {type: 'input'}},
                    {title: 'Изображение', value: 'images', width: '15%', sort: false},
                    {title: 'Имя', value: 'name', width: '30%', filter: {type: 'input'}},
                    {title: 'Кол-во цен', value: 'link_count', width: '5%'},
                    {title: 'Ингред.', value: 'is_ingredients_exist', width: '3%'},
                    {title: 'Объем', value: 'volume', width: '5%', filter: {type: 'input'}},
                    {title: 'Статус', value: 'status', width: '5%', filter: { type: 'select' }},
                    {title: 'Юзер', value: 'user_name', width: '5%'},
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
                    category: this.availableCategoryNames.map(el => ({ title: el, value: el })),
                    status: ['moderated', 'published', 'rejected', 'deleted'].map(el => ({ title: el, value: el })),
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
    @import './resources/css/admin/table.scss';
    .status {
        color: white;
        border-radius: 20px;
        background-color: red;
        padding: 5px;
    }
</style>
