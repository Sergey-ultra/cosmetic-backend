<template>
    <div v-if="myStore" class="store">
        <div v-if="myStore.store_id !== null" class="store__status">
            <span class="store__icon">
                <svg data-v-27f03725="">
                    <use xlink:href="#icons_question-circle" data-v-27f03725=""><symbol viewBox="0 0 24 24" id="icons_question-circle" data-v-27f03725=""><path fill-rule="evenodd" d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11 11-4.925 11-11S18.075 1 12 1m0 1.75c5.1 0 9.25 4.15 9.25 9.25S17.1 21.25 12 21.25 2.75 17.1 2.75 12 6.9 2.75 12 2.75zm-1.5 13.293c0 .718.479 1.162 1.244 1.162.766 0 1.237-.444 1.237-1.162 0-.725-.471-1.169-1.237-1.169-.765 0-1.244.444-1.244 1.169zm1.456-8.996c-2.099 0-3.288 1.176-3.322 2.878h1.852c.041-.752.561-1.237 1.347-1.237.779 0 1.299.451 1.299 1.086 0 .636-.267.964-1.149 1.491-.943.553-1.319 1.169-1.23 2.262l.014.376h1.811v-.362c0-.656.253-.991 1.162-1.518.964-.567 1.463-1.285 1.463-2.31 0-1.579-1.292-2.666-3.247-2.666z" data-v-27f03725=""></path></symbol></use>
                </svg>
            </span>
            <span>Привязан к существующему магазину</span>
        </div>
        <div class="store__header">

            <div class="store__title">
                <div class="store__img" v-if="myStore.image">
                    <img :src="myStore.image" alt="my_store_img">
                </div>
                <div class="store__description">
                    <div>{{ myStore.name }}</div>
                    <div>{{ myStore.file_url }}</div>
                </div>
            </div>
            <router-link
                :to="{ name: 'edit-store' }"
                class="store__edit"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                    <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                </svg>
            </router-link>
        </div>
    </div>

    <bind-store v-else-if="!isLoadingMyStore"/>
</template>

<script>
    import {mapActions, mapState} from "vuex";
    import bindStore from  '../../components/store/bind-store.vue'


    export default {
        name: "main",
        components: {
            bindStore,
        },
        data() {
            return {
            }
        },
        computed: {
            ...mapState('store', ['myStore', 'isLoadingMyStore'])
        },
        created() {
            this.loadMyStore()
        },
        methods: {
            ...mapActions('store', ['loadMyStore']),
        }
    }
</script>

<style scoped lang="scss">
    .store {
        margin-top: 20px;
        border-radius: 11px;
        background-color: #fff;
        padding-bottom: 20px;
        overflow: hidden;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.16);
        &__status {
            display: block;
            width: 600px;
            font-size: 15px;
            line-height: 18px;
            padding: 14px 16px 14px 20px;
            background-color: #fff8d9;
            & .store__icon {
                color: #ffa300;
                display: inline;
                margin: 8px;
                position: relative;
                & svg {
                    fill: #ffa300 !important;
                    height: 20px;
                    left: 50%;
                    position: absolute;
                    top: 50%;
                    transform: translate(-50%,-50%);
                    width: 20px;
                }
            }
            & span {
                color: #ffa300;
                display: inline;
                margin: 8px;
            }
        }
        &__header {
            width: 600px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            box-shadow: 0 2px 3px 0 rgba(0, 0, 0, 0.06);
        }
        &__title {
            display: flex;
            align-items: center;
            & > :not(:last-child) {
                margin-right: 10px;
            }
        }
        &__edit {
            cursor: pointer;
        }
        &__img {
            height: 60px;
            width: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
            & img {
                max-height: 100%;
                max-width: 100%;
            }

        }
    }
</style>
