<template>
    <form class="form__block" @input="setFormChangingToTrue">
        <div class="field__row">
            <div class="field__item field__item-img">тег картинки</div>
            <input class="field__item field__item-tag" type="text" v-model="options.imgTag">
            <input class="field__item field__item-tag" type="text" placeholder="Аттрибут ссылки картинки" v-model="options.imgAttr">
        </div>

        <div class="field__row">
            <div class="field__item field__item-img">поле в файле</div>
            <div class="field__item field__item-tag">тег на сайте</div>
            <div class="field__item field__item-number">Какой по счету</div>
            <div class="field__item field__item-is-required">Есть вложенный</div>
        </div>
        <div class="field">
            <div>
                <div
                        v-for="(field, index) in options.fileFields"
                        :key="index"
                        class="field__row"
                >
                    <select class="field__item field__item-img"  v-model="field[0]">
                        <option value="name">name</option>
                        <option value="country">country</option>
                        <option value="brand">brand</option>
                        <option value="volume">volume</option>
                        <option value="price">price</option>
                        <option value="description">description</option>
                        <option value="ingredient">ingredient</option>
                        <option value="application">application</option>
                        <option value="purpose">purpose</option>
                        <option value="effect">effect</option>
                        <option value="age">age</option>
                        <option value="type_of_skin">type_of_skin</option>
                    </select>
                    <input class="field__item field__item-tag" type="text" v-model="field[1]">
                    <input class="field__item field__item-number" type="text" placeholder="Какой по счету элемент" v-model="field[2]">


                    <input class="field__item field__item-number" type="text" placeholder="Введите titleTag" v-model="field[3]">
                    <input class="field__item field__item-number" type="text" placeholder="Введите titleValue" v-model="field[4]">
                    <input class="field__item field__item-number" type="text" placeholder="Введите valueTag" v-model="field[5]">

                </div>
            </div>
            <div class="add__field" @click="addInput">+</div>
            <div class="delete__field" @click="removeInput">-</div>
        </div>
        <div class="form__element">
            <button
                    type="button"
                    :disabled="!(isFormChanging && storeId !== null)"
                    @click="save"
            >
                Сохранить настройки
            </button>
        </div>
    </form>
</template>

<script>
    import {mapActions, mapState} from "vuex";

    export default {
        name: "product-options-form",
        data() {
            return {
                isFormChanging:false,
                options: {
                    imgTag: "",
                    imgAttr: "",
                    fileFields: [
                        []
                    ],
                },
            }
        },
        props:{
            storeId:{
                default: null
            }
        },
        computed: {
            ...mapState('productOptions', ['productOptions']),
        },
        watch: {
            storeId(value) {
                this.isFormChanging = false
                if (value) {
                    this.loadProductOptions(value)
                }
            },
            productOptions(value) {
                this.options = {...value}
            },
        },
        created() {
            if (this.storeId) {
                this.loadProductOptions(this.storeId)
            }
        },
        methods: {
            ...mapActions('productOptions', ['loadProductOptions', 'saveProductOptions']),
            setFormChangingToTrue() {
                this.isFormChanging = true
            },
            async save() {
                this.isFormChanging = false

                this.saveProductOptions({
                    store_id: this.storeId,
                    options: this.options
                })
            },
            addInput() {
                this.options.fileFields.push(['','', null, true])
            },
            removeInput() {
                if (this.options.fileFields.length > 4)
                    this.options.fileFields.pop()
            },
        }
    }
</script>

<style scoped lang="scss">
    select option {
        display: flex;
        justify-content: space-between;
        width: 200px;

        & div {
            padding: 0 10px;
        }
    }
    .option__not-empty {
        background: #84f4bd;
    }
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

    .parser__settings {
        display:flex;
        justify-content: space-between;
    }



    .target__url {
        width:500px;
    }
    .parser {
        display: flex;
        flex-direction:column;
    }
    .form__group {
        display: flex;
        justify-content: space-between;
    }
    .form__element {
        margin-top: 15px;
    }
    .form__block {
        background-color: rgba(0, 0, 0, 0.08);
        border-radius:5px;
        border: 1px solid rgba(0,0,0,0.55);
        margin:29px 0;
        box-shadow: 0px 1px 4px rgba(0,0,0,0.15);
        padding:20px;
    }
    .field {
        display:flex;
        align-items:flex-end;
        &__row {
            display:flex;
            margin-top:10px;

        }
        &__item {
            &:not(last-child) {
                margin-right:15px;
            }
            &-img {
                min-width:200px;

            }
            &-tag {
                width:400px;
            }
            &-number {
                width:150px;
            }
            &-is-required {
                width: 40px;
            }
        }
    }

    .svg-loader{
        height: 100%;
        display:flex;
        position: relative;
        align-content: space-around;
        justify-content: center;
    }
    .loader-svg{
        position: absolute;
        left: 0; right: 0; top: 0; bottom: 0;
        fill: none;
        stroke-width: 5px;
        stroke-linecap: round;
        stroke: rgb(64, 0, 148);
    }
    .loader-svg.bg{
        stroke-width: 8px;
        stroke: rgb(207, 205, 245);
    }




    .add__field,
    .delete__field {
        display:flex;
        align-items:center;
        justify-content: center;
        margin-left:15px;
        padding: 0 10px;
        background-color: #fff;
        border:none;
        height:25px;
        box-shadow: 1px 0 0 #f3c8d2, -1px 0 0 #f3c8d2, 0 -1px 0 rgba(243,200,210,0.5), 0 4px 7px rgba(243,200,210,0.4), 0 1px 1px #f3c8d2;
    }

    .table-block {
        position:relative;
    }
    .table__layer {
        position: absolute;
        top:0;
        bottom:0;
        left:0;
        right:0;
        background-color: rgba(255, 255, 255, 0.81);
    }
</style>