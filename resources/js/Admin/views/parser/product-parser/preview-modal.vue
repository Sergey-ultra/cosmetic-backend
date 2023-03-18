<template>
    <modal
            :width="80"
            v-model:isShowForm="isShowForm"
    >
        <template v-slot:header>Спарсенная инфа</template>

        <div class="preview__wrapper" v-for="(product, productIndex) in preview" :key="productIndex">
            <div class="preview__row" v-for="(value, name, index) in product" :key="index">
                <div class="preview__item preview__title">{{ name }}</div>

                <div class="preview__item preview__content" v-if="['images','imageLinks'].includes(name)">
                    <img class="image" v-for="image in value" :key="image" :src="image"/>
                </div>
                <div class="preview__item preview__content" v-else-if="name === 'ingredient'">
                    <div v-for="ingredient in value" :key="ingredient">{{ ingredient }}</div>
                </div>
                <div class="preview__item preview__content" v-else>
                    <span>{{ value }}</span>
                </div>
            </div>
        </div>

        <template v-slot:buttons>
            <button class="button" type="button" @click="$emit('update:isShowForm', false)">Отмена</button>
        </template>
    </modal>
</template>

<script>
    import modal from "../../../components/modal/modal.vue"
    import {mapState} from "vuex";

    export default {
        name: "preview-modal",
        components: {
            modal
        },
        data() {
            return {
            }
        },
        props:{
            isShowForm:{
                type: Boolean,
                default: false
            }
        },
        computed: {
            ...mapState('productParser', ['preview']),
        }
    }
</script>

<style scoped lang="scss">
    button {
        min-width: 28px;
        padding: 0 12.4px;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 4px;
        color: #fff;
        height: 28px;
        background: rgb(24, 103, 192) none repeat scroll 0% 0%;
        border: 1px solid rgb(24, 103, 192);
        &[disabled] {
            background: #ebeff4;
        }
    }
    .image {
        background-size: 32px 32px;
        border: 0;
        border-radius: 50%;
        cursor: pointer;
        display: block;
        margin: 15px;
        width: 100px;
        height: 100px;
        &:hover {
            transform: scale(1.2);
            transition: all .2s ease;
        }
    }
    .preview {
        &__wrapper {
            border: 1px solid #999999;
            border-radius: 4px;
            margin:15px 0;
        }
        &__row {
            display: flex;
            padding: 5px 0;
            &:not(:last-child) {
                border-bottom: 1px solid #999999;
            }
        }
        &__item {
            display: flex;
            align-items: center;
            padding: 0 10px;
        }
        &__title {
            width:10%;
            font-weight: bold;
            padding-right:15px;
        }
        &__content {
            width:90%;
            flex-wrap: wrap;
        }
    }
</style>
