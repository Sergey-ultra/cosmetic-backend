<template>
    <data-table
        :items="videos"
        :headers="headers"
        :isLoading="isLoadingVideos"
        :optionsItemsPerPage="optionsItemsPerPage"
        :total="total"
        :availableOptions="availableOptions"
        v-model:options="options"
        v-model:filter="filter"
        @reloadTable="reloadVideos"
    >

        <template v-slot:add>
            <btn @click="showForm(null)">
                Добавить
            </btn>
        </template>

        <template v-slot:product_name="video">
            <a :href="video.item.product_link">
                <span>{{ video.item.product_name }}</span>
            </a>
        </template>

        <template v-slot:image="video">
            <div class="flex">
                <img class="image" :src="video.item.image" :alt="video.item.product_name">
            </div>
        </template>
        <template v-slot:thumbnail="video">
            <div class="flex">
                <img class="image" :src="video.item.thumbnail" :alt="video.item.product_name">
            </div>
        </template>

        <template v-slot:action="video">
            <div class="action">
                <drop-menu :items="['publish', 'moderated', 'rejected', 'deleted']">
                    <template v-slot:publish
                              v-if="video.item.status !== 'published'"
                    >
                        <div class="dropdown__inner">
                            <button
                                class="dropdown__value"
                                @click="setStatus({ id: video.item.id, status: 'published' })"
                            >
                                <span>Опубликовать</span>
                            </button>
                        </div>
                    </template>
                    <template v-slot:moderated v-if="video.item.status !== 'moderated'">
                        <div class="dropdown__inner">
                            <button
                                class="dropdown__value"
                                @click="setStatus({ id: video.item.id, status: 'moderated' })"
                            >
                                <span>Поставить на модерацию</span>
                            </button>
                        </div>
                    </template>
                    <template v-slot:rejected  v-if="video.item.status !== 'rejected'">
                        <div class="dropdown__inner">
                            <button
                                class="dropdown__value"
                                @click="setReviewStatus({ id: video.item.id, status: 'rejected' })"
                            >
                                <span>Отклонить</span>
                            </button>
                        </div>
                    </template>
                    <template v-slot:deleted
                              v-if="video.item.status !== 'deleted'">
                        <div class="dropdown__inner">
                            <button
                                class="dropdown__value"
                                @click="setStatus({ id: video.item.id, status: 'deleted' })"
                            >
                                <span>Удалить</span>
                            </button>
                        </div>
                    </template>
                </drop-menu>
            </div>
            <div class="action" @click="showForm(video.item.id)">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     class="bi bi-pencil-fill" viewBox="0 0 16 16">
                    <path
                        d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                </svg>
            </div>
        </template>
    </data-table>
</template>

<script>
import {mapActions, mapMutations, mapState} from "vuex";
import dataTable from "../../components/data-table.vue";
import modal from "../../components/modal/modal.vue";
import btn from "../../components/btn.vue";
import dropMenu from "../../components/drop-menu.vue";

export default {
    name: "index",
    components:{
        dataTable,
        modal,
        btn,
        dropMenu
    },
    data: () => ({
        headers: [
            {title: 'id', value: 'id', width: '5%'},
            {title: 'Картинка', value: 'image', width: '5%', sort: false},
            {title: 'Имя sku', value: 'product_name', width: '20%'},
            {title: 'Юзер', value: 'user_name', width: '10%', filter: {type: 'input'}},
            {title: 'Миниатюра видео', value: 'thumbnail', width: '5%', sort: false},
            {title: 'Описание', value: 'description', width: '25%'},
            {title: 'Статус', value: 'status', width: '10%', filter: { type: 'select' }},
            {title: 'Дата', value: 'created_at', width: '10%'},
            {title: 'Действия', value: 'action', width: '10%'}
        ],
        optionsItemsPerPage: [5, 10, 20, 50, 100],
        isShowForm: false,
        selectedVideoId: null,
        selectedName: null,
    }),
    computed:{
        ...mapState('video', ['tableOptions', 'filterOptions', 'isLoadingVideos', 'videos', 'total']),
        availableOptions() {
            return {
                status: ['moderated', 'published', 'rejected', 'deleted']
            }
        },
        filter: {
            get() {
                return this.filterOptions
            },
            set(value) {
                this.setFilterOptions(value)
                this.loadVideos()
            }
        },
        options:{
            get() {
                return this.tableOptions
            },
            set(value) {
                this.setTableOptions(value);
                this.loadVideos();
            }
        }
    },
    created() {
        if (!this.videos.length) {
            this.loadVideos();
        }
    },
    methods: {
        ...mapActions('video', ['reloadVideos', 'loadVideos', 'setStatus']),
        ...mapMutations('video', ['setTableOptions', 'setFilterOptions']),
        showForm(id) {
            this.isShowForm = true
            this.selectedVideoId = id
        },
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
