<template>
    <div>
        <h4>Заявка на размещение прайса</h4>
        <div v-if="!isShowStoreUrlForm" class="form">

            <div v-if="!isShowCreateForm">
                <div class="form__row">
                    <select-store v-model:selectedStore="selectedStore"/>
                </div>

                <div v-if="selectedStore === null">
                    <div  class="form__row" @click="showCreateForm">
                        <button class="form__button" type="button">Или создайте новый магазин</button>
                    </div>
                </div>
                <div v-else class="form__item form__row" @click="showStoreUrlForm">
                    <button class="form__button" type="submit">Далее</button>
                </div>

            </div>
            <add-store
                v-model:isShowCreateForm="isShowCreateForm"
                v-else
            />
        </div>

        <add-price-file-url
            v-else
            v-model:isShowStoreUrlForm="isShowStoreUrlForm"
            :storeId="selectedStore.id"
        />
    </div>
</template>

<script>
import {mapActions, mapState} from "vuex";
import selectStore from  '../../components/store/select-store.vue'
import addStore from  '../../components/store/add-store.vue'
import addPriceFileUrl from  '../../components/store/add-price-file-url.vue'

export default {
    name: "bind-store",
    components: {
        addStore,
        selectStore,
        addPriceFileUrl
    },
    data() {
        return {
            selectedStore:  null,
            isShowCreateForm: false,
            isShowStoreUrlForm: false,
        }
    },
    methods: {
        showCreateForm() {
            this.isShowCreateForm = true
        },
        showStoreUrlForm() {
            this.isShowStoreUrlForm = true
        },
    }
}
</script>

<style scoped lang="scss">
.form {
    width: 600px;
    &__row {
        width:100%;
        margin-bottom: 20px;
    }
    &__item {
        display: flex;
        justify-content: center;
        flex-wrap:wrap;
        &-full {
            width: 100%;
        }
        &:not(:last-child) {
            margin-right: 10px;
        }
    }
    &__button {
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 40px;
        border: 1px solid #ccc;
        border-radius: 4px;
        color: #000;
        text-transform: uppercase;
        font-weight:bold;
        background-color: #fff;
        transition: background-color 0.3s ease 0s, border-color 0.3s ease 0s;
        padding: 0 30px;
        &:hover {
            background-color: #dcdcdc;
        }
        &-active {
            background-color: #1867c0;
            color: #fff;
            border: none;
            &:hover {
                background-color: #3b75c0;
            }
        }
    }
}




.input {
    width: 100%;
    outline: #000 none medium;
    overflow: visible;
    transition: background-color 0.3s ease 0s, border-color 0.3s ease 0s;
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 8px;
    &:hover {
        border-color: rgb(192, 201, 240);
        transition: border-color 0.3s ease 0s;
    }
    &:focus {
        border-color: rgb(59, 87, 208);
        transition: background-color 0.3s ease 0s, border-color 0.3s ease 0s;
    }
}
</style>
