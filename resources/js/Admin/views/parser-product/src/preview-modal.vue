<template>
    <modal
            :width="80"
            v-model:isShowForm="isShowFormLocal"
    >
        <template v-slot:header>Спарсенная инфа</template>

        <div class="preview__wrapper" v-for="(product, productIndex) in preview" :key="productIndex">
            <div class="preview__row" v-for="(value, name, index) in product" :key="index">
                <div class="preview__item preview__title">{{ name }}</div>

                <div v-if="['images','imageLinks'].includes(name)" class="preview__item preview__content" >
                    <img class="image" v-for="image in value" :key="image" :src="image"/>
                </div>
                <div
                    v-else-if="name === 'ingredient'"
                    class="preview__item preview__content"
                    :class="{'preview__content-ingredients': name === 'ingredient'}"
                >
                    <div v-for="ingredient in value" :key="ingredient">{{ ingredient }}</div>
                </div>
                <div v-else class="preview__item preview__content">
                    <span>{{ value }}</span>
                </div>
            </div>
        </div>

        <template v-slot:buttons>
            <buttonComponent :size="'small'" :color="'blue'" @click="$emit('update:isShowForm', false)">Отмена</buttonComponent>
        </template>
    </modal>
</template>

<script>
    import modal from "../../../components/modal/modal.vue"
    import {mapState} from "vuex";
    import buttonComponent from "../../../components/button-component.vue";

    export default {
        name: "preview-modal",
        components: {
            modal,
            buttonComponent
        },
        props:{
            isShowForm:{
                type: Boolean,
                default: false
            }
        },
        computed: {
            ...mapState('productParser', ['preview']),
            isShowFormLocal: {
                get() {
                    return this.isShowForm;
                },
                set(value) {
                    this.$emit('update:isShowForm', value)
                }
            }
        }
    }
</script>

<style scoped lang="scss">
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
            &-ingredients {
                align-items: stretch;
                flex-direction: column;
            }
        }
    }
</style>
