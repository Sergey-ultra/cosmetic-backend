<template>
    <data-table
        :isLoading="isLoading"
        :optionsItemsPerPage="optionsItemsPerPage"
        :total="total"
        :items="brands"
        :headers="headers"
        :availableOptions="availableOptions"
        v-model:options="options"
        v-model:filter="filter"
        @reloadTable="reloadBrands"
    >
        <template v-slot:add>
           <btn @click="showForm(null)">Добавить</btn>
        </template>

        <template v-slot:image="brand">
            <img v-if="brand.item.image" class="image" :src="brand.item.image" :alt="brand.item.image">
        </template>

        <template v-slot:action="brand">
            <div class="action" @click="showForm(brand.item.id)">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     class="bi bi-pencil-fill" viewBox="0 0 16 16">
                    <path
                        d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                </svg>
            </div>
            <div class="action" @click="showDeleteForm(brand.item)">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     class="bi bi-trash-fill" viewBox="0 0 16 16">
                    <path
                        d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z"/>
                </svg>
            </div>
        </template>
    </data-table>

    <brand-form
            v-if="isShowForm"
            v-model:isShowForm="isShowForm"
            :selectedBrandId="selectedBrandId"
    />

    <delete-form
            :selectedName="selectedName"
            v-if="isShowDeleteForm"
            v-model:isShowDeleteForm="isShowDeleteForm"
            @delete="deleteBrand"
    />
</template>

<script>
    import dataTable from '../../components/data-table.vue'
    import modal from '../../components/modal/modal.vue'
    import btn from '../../components/btn.vue'
    import brandForm from './brand-form.vue'
    import deleteForm from './../../components/delete-form.vue'
    import {mapActions, mapGetters, mapMutations, mapState} from "vuex";

    export default {
        name: "brands",
        components: {
            dataTable,
            modal,
            brandForm,
            btn,
            deleteForm
        },
        data: () => ({
            headers: [
                {title: 'id', value: 'id', width: '5%'},
                {title: 'Имя', value: 'name', width: '20%', filter: { type: 'input' }},
                {title: 'Код', value: 'code', width: '10%', filter: { type: 'input' }},
                {title: 'Картинка', value: 'image', width: '10%', sort: false},
                {title: 'Описание', value: 'description', width: '22%', filter: { type: 'input' }},
                {title: 'Кол-во sku', value: 'sku_count', width: '10%'},
                {title: 'Страна', value: 'country', width: '15%', filter: { type: 'select' }},
                {title: 'Действия', value: 'action', width: '8%'}
            ],
            optionsItemsPerPage: [5, 10, 20, 50, 100],
            isShowForm: false,
            selectedBrandId: null,
            isShowDeleteForm:false,
            selectedName: null,
        }),
        computed:{
            ...mapState('brand', ['tableOptions', 'filterOptions', 'isLoading', 'brands', 'total', 'availableCountries']),
            ...mapGetters('country', ['availableCountryNames']),
            availableOptions() {
                return {
                    country: this.availableCountryNames
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
                    this.loadBrands()
                }
            }
        },
        created() {
            if (!this.brands.length) {
                this.loadBrands()
            }

            if (!this.availableCountryNames.length) {
                this.loadAllCountries()
            }
        },
        watch: {

        },
        methods: {
            ...mapActions('brand', ['reloadBrands', 'loadBrands', 'deleteItem',]),
            ...mapActions('country', ['loadAllCountries']),
            ...mapMutations('brand', ['setTableOptions', 'setFilterOptions']),
            showForm(id){
                this.isShowForm = true
                this.selectedBrandId = id
            },
            showDeleteForm(item) {
                this.isShowDeleteForm = true
                this.selectedBrandId = item.id
                this.selectedName = item.name
            },
            deleteBrand() {
                this.deleteItem(this.selectedBrandId)
                this.isShowDeleteForm = false
            }
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
    .image {
        height: 32px;
        width: 32px;
        display: block;
        margin: 0;
        background-size: 32px 32px;
        border: 0;
        border-radius: 50%;
        cursor:pointer;
        &:hover {
            transition: all 0.2s ease;
            transform: scale(1.2);
        }
    }
</style>
