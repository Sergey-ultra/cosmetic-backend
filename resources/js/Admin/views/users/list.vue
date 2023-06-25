<template>
    <data-table
        :isLoading="isLoading"
        :optionsItemsPerPage="optionsItemsPerPage"
        :total="total"
        :items="users"
        :headers="headers"
        :availableOptions="availableOptions"
        v-model:options="options"
        v-model:filter="filter"
        @reloadTable="reloadUsers"
    >
        <template v-slot:add>
            <buttonComponent :size="'small'" :color="'blue'" @click="showForm(null)">Добавить</buttonComponent>
        </template>

        <template v-slot:avatar="user">
            <img v-if="user.item.avatar" class="image" :src="user.item.avatar" :alt="user.item.avatar">
        </template>

        <template v-slot:role_id="user">
            <span>
                {{ availableRoles.filter(el => user.item.role_id === el.id)[0]?.name }}
            </span>
        </template>

        <template v-slot:action="user">
            <buttonComponent
                class="action"
                @click="showForm(user.item.id)"
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
                @click="showDeleteForm(user.item)"
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

    <user-form
        :selectedUserId="selectedUserId"
        v-if="isShowForm"
        v-model:isShowForm="isShowForm"
    />

    <delete-form
        :selectedName="selectedName"
        v-if="isShowDeleteForm"
        v-model:isShowDeleteForm="isShowDeleteForm"
        @delete="deleteUser"
    />

</template>

<script>
import {mapActions, mapGetters, mapMutations, mapState} from "vuex";
import dataTable from '../../components/data-table.vue'
import userForm from './user-form.vue'
import deleteForm from '../../components/delete-form.vue'
import buttonComponent from "../../components/button-component.vue";

export default {
    name: "users",
    components: {
        dataTable,
        buttonComponent,
        userForm,
        deleteForm
    },
    data () {
        return {
            headers: [
                {title: 'id', value: 'id', width: '3%'},
                {title: 'Аватар', value: 'avatar', width: '5%', sort: false},
                {title: 'Имя', value: 'name', width: '12%'},
                {title: 'E-mail', value: 'email', width: '15%'},
                {title: 'Сервис', value: 'service', width: '15%'},
                {title: 'Баланс', value: 'balance', width: '5%'},
                {title: 'Реф. Баланс', value: 'referral_balance', width: '5%'},
                {title: 'Реф. ссылка', value: 'ref', width: '15%'},
                {title: 'Реф. юзер', value: 'referral_owner', width: '10%'},
                {title: 'Роль', value: 'role_id', width: '5%', filter: {type: 'select'}},
                {title: 'Дата регистрации', value: 'created_at', width: '5%'},
                {title: 'Действия', value: 'action', width: '5%'},
            ],
            optionsItemsPerPage: [5, 10, 20, 50, 100],
            isShowForm: false,
            selectedUserId: null,
            isShowDeleteForm: false,
            selectedName: null
        }
    },
    computed: {
        ...mapState('user', ['tableOptions', 'filterOptions', 'isLoading', 'users', 'total', 'availableRoles']),
        availableOptions() {
            return {
                role_id: this.availableRoles.map(el => ({ title: el.name, value: el.id })),
            }
        },
        filter: {
            get() {
                return this.filterOptions
            },
            set(value) {
                this.setFilterOptions(value)
                this.loadUsers()
            }
        },
        options: {
            get() {
                return this.tableOptions
            },
            set(value) {
                this.setTableOptions(value)
                this.loadUsers()
            }
        }
    },
    created() {
        if (!this.users.length) {
            this.loadUsers()
        }
        if (!this.availableRoles.length) {
            this.loadAvailableRoles()
        }
    },
    watch: {

    },
    methods:{
        ...mapActions('user', ['reloadUsers', 'loadUsers', 'loadAvailableRoles', 'deleteItem']),
        ...mapMutations('user', ['setTableOptions', 'setFilterOptions']),
        showForm(id) {
            this.isShowForm = true
            this.selectedUserId = id
        },
        showDeleteForm(item) {
            this.isShowDeleteForm = true
            this.selectedUserId = item.id
            this.selectedName = item.name + ' ' + item.email
        },
        deleteUser() {
            this.deleteItem(this.selectedUserId)
            this.isShowDeleteForm = false
        }
    }
}
</script>

<style scoped lang="scss">
@import './resources/css/admin/table.scss';
</style>
