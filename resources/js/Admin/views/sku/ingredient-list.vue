<template>
    <div v-for="(ingredient, index) in ingredients">
        <div class="flex">
            {{ index + 1 }}
            <select v-model="ingredient.ingredient_id" class="form-control" :key="index">
                <option @click="addIngredientIntoList">Добавить новый игредиент</option>
                <option v-for="(ingredient,index) in allIngredients" :value="ingredient.id" :key="index">{{
                    ingredient.name_en }}
                </option>
            </select>
            <div class="flex">
                <btn class="minus">-</btn>
                <btn class="plus">+</btn>
            </div>
        </div>
    </div>
    <btn @click="addIngredient">Добавить ингредиент</btn>
</template>

<script>
    import {mapActions, mapState} from "vuex";
    import btn from '../../components/btn.vue'

    export default {
        name: "ingredient-list",
        components: {
            btn
        },
        props: {
            ingredients: {
                type: Array
            }
        },
        computed: {
            ...mapState('ingredient', ['allIngredients']),
        },
        created() {
            if (!this.allIngredients.length) {
                this.loadAllIngredients()
            }
        },
        methods: {
            ...mapActions('ingredient', ['loadAllIngredients']),
            addIngredientIntoList() {
                this.$router.push({name: 'ingredient_create'})
            },
            addIngredient() {
                let localOrder = this.ingredients.length + 1
                this.ingredients.push({order: localOrder})
            },
        }
    }
</script>

<style scoped>
    .flex {
        display: flex;
        align-items: center;
    }

    .flex > * :not(:last-child) {
        margin-right: 15px;
    }

    .minus,
    .plus {
        padding: 0;
    }
</style>
