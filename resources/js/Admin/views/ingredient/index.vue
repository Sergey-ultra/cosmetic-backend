<template>
    <data-table
        :items="ingredients"
        :headers="headers"
        :isLoading="isLoading"
        :optionsItemsPerPage="optionsItemsPerPage"
        :total="total"
        :availableOptions="availableOptions"
        v-model:options="options"
        v-model:filter="filter"
        @reloadTable="reloadIngredients"
    >

        <template v-slot:add>
            <buttonComponent :size="'small'" :color="'blue'" @click="showForm(null)">Добавить</buttonComponent>
        </template>
        <template v-slot:action="ingredient">
            <buttonComponent
                class="action"
                @click="showForm(ingredient.item.id)"
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
                @click="showDeleteForm(ingredient.item)"
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

    <ingredient-form
        :selectedIngredientId="selectedIngredientId"
        v-model:isShowForm="isShowForm"
        v-if="isShowForm"
    />

    <delete-form
            :selectedName="selectedName"
            v-if="isShowDeleteForm"
            v-model:isShowDeleteForm="isShowDeleteForm"
            @delete="deleteIngredient"
    />

</template>

<script>
    import dataTable from '../../components/data-table.vue'
    import modal from '../../components/modal/modal.vue'
    import ingredientForm from './ingredient-form.vue'
    import deleteForm from '../../components/delete-form.vue'
    import {mapActions, mapGetters, mapMutations, mapState} from "vuex";
    import buttonComponent from "../../components/button-component.vue";

    export default {
        name: "ingredients",
        components: {
            dataTable,
            modal,
            buttonComponent,
            ingredientForm,
            deleteForm,
        },
        data() {
            return {
                headers: [
                    {title: 'id', value: 'id', width: '2%'},
                    {title: 'name', value: 'name', width: '25%', filter: {type: 'input'}},
                    {title: 'name_rus', value: 'name_rus', width: '20%', filter: {type: 'input'}},
                    {title: 'code', value: 'code', width: '22%', filter: {type: 'input'}},
                    {title: 'description', value: 'description', width: '15%', filter: {type: 'input'}},
                    {title: 'активный', value: 'active_ingredients_group_name', width: '9%', filter: {type: 'select'}},
                    {title: 'Действия', value: 'action', width: '7%'}
                ],
                optionsItemsPerPage: [5, 10, 20, 50, 100],
                isShowForm: false,
                selectedIngredientId: null,
                isShowDeleteForm: false,
                selectedName: null,
            }
        },
        computed: {
            ...mapState('ingredient', ['tableOptions', 'filterOptions', 'availableActiveIngredientsGroups', 'isLoading', 'ingredients', 'total']),
            availableOptions() {
                return {
                    active_ingredients_group_name: this.availableActiveIngredientsGroups.map(el => ({ title: el.name, value: el.value })),
                }
            },
            filter: {
                get() {
                    return this.filterOptions
                },
                set(value) {
                    this.setFilterOptions(value)
                    this.loadIngredients()
                }
            },
            options: {
                get() {
                    return this.tableOptions
                },
                set(value) {
                    this.setTableOptions(value)
                    this.loadIngredients()
                }
            }
        },
        created() {
            if (!this.ingredients.length) {
                this.loadIngredients()
            }

            if (!this.availableActiveIngredientsGroups.length) {
                this.loadAvailableActiveIngredientsGroups()
            }
        },
        watch: {

        },
        methods: {
            ...mapActions('ingredient', ['reloadIngredients', 'loadIngredients', 'deleteItem', 'loadAvailableActiveIngredientsGroups']),
            ...mapMutations('ingredient', ['setTableOptions', 'setFilterOptions']),
            showForm(id) {
                this.isShowForm = true
                this.selectedIngredientId = id
            },
            showDeleteForm(item) {
                this.isShowDeleteForm = true
                this.selectedIngredientId = item.id
                this.selectedName = item.name
            },
            deleteIngredient() {
                this.deleteItem(this.selectedIngredientId)
                this.isShowDeleteForm = false
            }
        }
    }
</script>

<style scoped lang="scss">
   @import './resources/css/admin/table.scss';
</style>
