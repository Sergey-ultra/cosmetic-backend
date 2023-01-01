<template>
    <data-table
        :isLoading="isLoadingSuppliers"
        :optionsItemsPerPage="optionsItemsPerPage"
        :total="total"
        :items="suppliersWithPagination"
        :headers="headers"
        v-model:options="options"
        v-model:filter="filter"
        @reloadTable="reloadSuppliers"
    >

        <template v-slot:image="supplier">
            <img v-if="supplier.item.image" class="image" :src="supplier.item.image" :alt="supplier.item.image">
        </template>

        <template v-slot:action="supplier">
            <div class="action">
                <drop-menu :items="['published']">
                    <template v-slot:published>
                        <div class="dropdown__inner">
                            <button class="dropdown__value"
                                    v-if="supplier.item.status !== 'published'"
                                    @click="setStatus({id: supplier.item.id, status: 'published'})"
                            >
                                <span>Опубликовать</span>
                            </button>
                            <button
                                class="dropdown__value"
                                v-if="supplier.item.status !== 'moderated'"
                                @click="setStatus({id: supplier.item.id, status: 'moderated'})"
                            >
                                <span>Поставить на модерацию</span>
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
import dropMenu from '../../components/drop-menu.vue'
import btn from "../../components/btn.vue";
import {mapActions, mapMutations, mapState} from "vuex";

export default {
    components:{
        btn,
        dataTable,
        dropMenu
    },
    name: "store",
    data() {
        return {
            headers: [
                {title: 'id', value: 'id', width: '5%'},
                {title: 'name', value: 'name', width: '15%'},
                {title: 'link', value: 'link', width: '10%'},
                {title: 'image', value: 'image', width: '15%', sort: false},
                {title: 'Статус', value: 'status', width: '10%'},
                {title: 'Ссылка  на файл цен', value: 'file_url', width: '25%'},
                {title: 'Id привязанного магазина', value: 'store_id', width: '10%'},
                {title: 'Действия', value: 'action', width: '10%'}
            ],
            optionsItemsPerPage: [5, 10, 20, 50, 100]
        }
    },
    computed:{
        ...mapState('supplier',['suppliersWithPagination','tableOptions', 'filterOptions', 'isLoadingSuppliers', 'total']),
        filter: {
            get() {
                return this.filterOptions
            },
            set(value) {
                this.setFilterOptions(value)
                this.loadSuppliersWithPagination()
            }
        },
        options: {
            get() {
                return this.tableOptions
            },
            set(value) {
                this.setTableOptions(value)
                this.loadSuppliersWithPagination()
            }
        }
    },
    watch: {

    },
    created() {
        this.loadSuppliersWithPagination()
    },
    methods:{
        ...mapActions('supplier', ['reloadSuppliers', 'loadSuppliersWithPagination', 'setStatus']),
        ...mapMutations('supplier', ['setTableOptions', 'setFilterOptions']),
    }
}
</script>

<style lang="scss" scoped>
.action {
    cursor: pointer;
    &:not(:last-child) {
        margin-right: 20px;
    }
}
.image {
    height: 25px;
    width: 100px;
    display: block;
    margin : 5px;
    background-size: 32px 32px;
    border: 0;
    cursor: pointer;
    &:hover {
        transition: all 0.2s ease;
        transform: scale(1.2);
    }
}
.dropdown {
    margin: -5px 5px;
    &__inner {
        font-size: 14px;
        line-height: 16px;
    }
    &__value {
        margin: 0;
        border: none;
        outline: none;
        outline-offset: 2px;
        background: transparent;
        display: block;
        width: 100%;
        padding: 8px 12px;
        color: #222;
        cursor: pointer;
        user-select: none;
        text-align: left;
    }
    &__icon {
        display: inline-block;
        position: relative;
        width: 32px;
        height: 32px;
        & svg {
            fill: rgba(0,0,0,.4);
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%,-50%);
            width: 20px;
            height: 20px;
        }
    }
}
</style>
