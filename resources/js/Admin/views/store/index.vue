<template>
    <data-table
            :isLoading="isLoadingStores"
            :optionsItemsPerPage="optionsItemsPerPage"
            :total="total"
            :items="stores"
            :headers="headers"
            v-model:options="options"
            v-model:filter="filter"
            @reloadTable="reloadStores"
    >
        <template v-slot:add>
            <buttonComponent :size="'small'" :color="'blue'" @click="showForm(null)">Добавить</buttonComponent>
        </template>

        <template v-slot:image="store">
            <img v-if="store.item.image" class="image" :src="store.item.image" :alt="store.item.image">
        </template>

        <template v-slot:price_parsing_status="store">
            {{ store.item.price_parsing_status === 1 ? 'Да' : 'Нет' }}
        </template>

        <template v-slot:check_images_count="store">
            {{ store.item.check_images_count === 1 ? 'Да' : 'Нет' }}
        </template>


        <template v-slot:action="store">
            <div class="action">
                <drop-menu :items="['parse', 'image_count']">
                    <template v-slot:parse>
                        <div class="dropdown__inner">
                            <button class="dropdown__value"
                                    v-if="store.item.price_parsing_status === 1"
                                    @click="changeStatusToNoParsing(store.item.id)"
                            >
                                <span>Не парсить</span>
                            </button>
                            <button
                                    class="dropdown__value"
                                    v-else-if="store.item.price_parsing_status === 0"
                                    @click="changeStatusToParsing(store.item.id)"
                            >
                                <span>Начать парсить</span>
                            </button>
                        </div>
                    </template>
                    <template v-slot:image_count>
                        <div class="dropdown__inner">
                            <button
                                    class="dropdown__value"
                                    v-if="store.item.check_images_count === 1"
                                    @click="changeCheckingImageCountStatus({ id: store.item.id, status: 0 })"
                            >
                                <span>Не проверять кол-во картинок</span>
                            </button>
                            <button
                                    class="dropdown__value"
                                    v-else-if="store.item.check_images_count === 0"
                                    @click="changeCheckingImageCountStatus({ id: store.item.id, status: 1 })"
                            >
                                <span>Проверять кол-во картинок</span>
                            </button>
                        </div>
                    </template>
                </drop-menu>
            </div>
            <buttonComponent
                class="action"
                @click="showForm(store.item.id)"
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
                @click="showDeleteForm(store.item)"
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

    <store-form
            :selectedStoreId="selectedStoreId"
            v-model:isShowForm="isShowForm"
    />

    <delete-form
            :selectedName="selectedName"
            v-if="isShowDeleteForm"
            v-model:isShowDeleteForm="isShowDeleteForm"
            @delete="deleteStore"
    />
</template>
<script>
    import dataTable from '../../components/data-table.vue'
    import dropMenu from '../../components/drop-menu.vue'
    import storeForm from "./store-form.vue";
    import deleteForm from '../../components/delete-form.vue'
    import {mapActions, mapMutations, mapState} from "vuex";
    import buttonComponent from "../../components/button-component.vue";

    export default {
        components:{
            buttonComponent,
            dataTable,
            storeForm,
            deleteForm,
            dropMenu
        },
        name: "store",
        data() {
            return {
                headers: [
                    {title: 'id', value: 'id', width: '5%'},
                    {title: 'name', value: 'name', width: '15%'},
                    {title: 'link', value: 'link', width: '20%'},
                    {title: 'image', value: 'image', width: '15%', sort: false},
                    {title: 'links_count', value: 'links_count', width: '10%'},
                    {title: 'Парсятся цены?', value: 'price_parsing_status', width: '10%'},
                    {title: 'check_images_count', value: 'check_images_count', width: '10%'},
                    {title: 'Рейтинг', value: 'rating', width: '5%'},
                    {title: 'Действия', value: 'action', width: '10%'}
                ],
                optionsItemsPerPage: [5, 10, 20, 50, 100],
                isShowForm: false,
                selectedStoreId: null,
                isShowDeleteForm:false,
                selectedName: null,
            }
        },
        computed:{
            ...mapState('store',['stores','tableOptions', 'filterOptions', 'isLoadingStores', 'total']),
            filter: {
                get() {
                    return this.filterOptions
                },
                set(value) {
                    this.setFilterOptions(value)
                    this.loadStores()
                }
            },
            options: {
                get() {
                    return this.tableOptions
                },
                set(value){
                    this.setTableOptions(value)
                    this.loadStores()
                }
            }
        },
        watch: {

        },
        created() {
            this.loadStores()
        },
        methods:{
            ...mapActions('store', ['reloadStores', 'loadStores', 'deleteItem', 'changePriceParsingStatus',
                'changeCheckingImageCountStatus']),
            ...mapMutations('store', ['setTableOptions', 'setFilterOptions']),
            showForm(id) {
                this.isShowForm = true
                this.selectedStoreId = id
            },
            showDeleteForm(item) {
                this.isShowDeleteForm = true
                this.selectedStoreId = item.id
                this.selectedName = item.name
            },
            deleteStore(){
                this.deleteItem(this.selectedStoreId)
                this.isShowDeleteForm = false
            },
            changeStatusToParsing(id) {
                this.changePriceParsingStatus({ id, status: 1 })
            },
            changeStatusToNoParsing(id) {
                this.changePriceParsingStatus({ id, status: 0})
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import './resources/css/admin/table.scss';
</style>
