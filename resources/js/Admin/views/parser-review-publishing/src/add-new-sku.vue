<template>
    <form class="form fill" @submit.prevent="createNewSku">
        <h3>Добавление нового объекта</h3>

<!--        <div class="form__group">-->
<!--            <label>-->
<!--                <div class="label">-->
<!--                    <span class="text-gray">Раздел:</span>-->
<!--                </div>-->
<!--                <selectComponent-->
<!--                    :color="'white'"-->
<!--                    v-model="topLevelCategoryId"-->
<!--                    :items="topLevelCategories"-->
<!--                    :item-title="'name'"-->
<!--                    :item-value="'id'"-->
<!--                />-->
<!--                <div class="invalid-feedback" v-for="error of v$.sku.category_id.$errors" :key="error.$uid">-->
<!--                    {{ error.$message }}-->
<!--                </div>-->
<!--            </label>-->
<!--        </div>-->

<!--        <div v-if="topLevelCategoryId && lowLevelCategories.length" class="form__group">-->
<!--            <label>-->
<!--                <div class="label">-->
<!--                    <span class="text-gray">Подраздел:</span>-->
<!--                </div>-->
<!--                <selectComponent-->
<!--                    :color="'white'"-->
<!--                    v-model="sku.category_id"-->
<!--                    :items="lowLevelCategories"-->
<!--                    :item-title="'name'"-->
<!--                    :item-value="'id'"-->
<!--                />-->
<!--            </label>-->
<!--        </div>-->

        <div class="form__group">
            <label>
                <div class="label">
                    <span class="text-gray">Точное название объекта отзыва:</span>
                </div>
                <inputComponent v-model="localSkuName" :color="'white'"/>
                <div class="invalid-feedback" v-for="error of v$.sku.name.$errors" :key="error.$uid">
                    {{ error.$message }}
                </div>
            </label>
        </div>

        <div class="form__group">
            <label>
                <div class="label">
                    <span class="text-gray">Бренд:</span>
                </div>
                <selectComponent
                    v-model="sku.brand_id"
                    :color="'white'"
                    :isSearch="true"
                    :placeholder="'Выберите бренд'"
                    :items="brands"
                    :item-title="'name'"
                    :item-value="'id'"
                >
                    <template v-slot:create>
                        <span class="brand" @click="showAddingBrand">Добавить бренд</span>
                    </template>
                </selectComponent>
                <div class="invalid-feedback" v-for="error of v$.sku.brand_id.$errors" :key="error.$uid">
                    {{ error.$message }}
                </div>
            </label>
        </div>

        <div v-if="isShowAddingBrand" class="brand__block">
            <h3>Добавление бренда</h3>
            <span @click="hideAddingBrand" class="brand__close">✕</span>

            <div class="form__group">
                <inputComponent v-model="newBrand" :color="'white'"/>
            </div>

            <buttonComponent @click="saveBrand" :color="'yellow'" :radius="true">
                Создать бренд
            </buttonComponent>
        </div>

        <div class="form__group">
            <label>
                <div class="label">
                    <span class="text-gray">Объем:</span>
                </div>
                <inputComponent v-model="sku.volume" :color="'white'"/>
                <div class="invalid-feedback" v-for="error of v$.sku.volume.$errors" :key="error.$uid">
                    {{ error.$message }}
                </div>
            </label>
        </div>

        <div class="form__group">
            <label>
                <div class="label">
                    <span class="text-gray">Описание:</span>
                </div>
                <textareaComponent rows="10" v-model="sku.description" :color="'white'"/>
                <div class="invalid-feedback" v-for="error of v$.sku.description.$errors" :key="error.$uid">
                    {{ error.$message }}
                </div>
            </label>
        </div>

        <div class="form__group">
            <label>
                <div class="label">
                    <span class="text-gray">Фотографии объекта отзыва:</span>
                </div>
                <multiple-image-upload
                    class="image-upload"
                    :entity="`sku`"
                    v-model:initialImageUrls="sku.images"
                />
                <div class="invalid-feedback" v-for="error of v$.sku.images.$errors" :key="error.$uid">
                    {{ error.$message }}
                </div>
            </label>
        </div>

        <buttonComponent type="submit" :color="'yellow'" :radius="true">
            Сохранить товарное предложение
        </buttonComponent>
    </form>
</template>
<script setup>
import multipleImageUpload from "../../../components/image-upload-as-form/image-upload.vue";
import textareaComponent from '../../../components/textarea-component.vue';
import buttonComponent from '../../../components/button-component.vue';
import selectComponent from '../../../components/select-component-extended.vue';
import inputComponent from '../../../components/input-component/index.vue';

import {helpers, minLength, required} from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import {useRouter} from "vue-router";
import { useStore } from 'vuex';
import {ref, defineProps, defineEmits, computed, onMounted} from "vue";

const store = useStore();
const router = useRouter();

const props = defineProps({
    name: {
        type: String,
        default: '',
    },
    categoryId: {
        type: Number,
    }
});

const emit = defineEmits(['hideAddForm', 'setNewSkuId']);

// const topLevelCategoryId = ref(null);
const isShowAddingBrand = ref(false);
const newBrand = ref('');
const sku = ref({
    name: '',
    brand_id: null,
});



const allBrands  = computed(() => store.state.brand.allBrands);
const localSkuName = computed({
    get() {
        if (sku.value.name){
            return sku.value.name;
        }
        return props.name;
    },
    set(value) {
        sku.value.name = value;
    }
});
const rules = {
    sku: {
        name: {
            required: helpers.withMessage('Поле должно быть заполнено', required),
            minLength: helpers.withMessage('Поле должно быть не меньше 3 символов', minLength(3)),
        },
        brand_id: {
            required: helpers.withMessage('Поле должно быть заполнено', required),
        },
        // category_id: {
        //     required: helpers.withMessage('Поле должно быть заполнено', required),
        // },
        volume: {
            required: helpers.withMessage('Поле должно быть заполнено', required),
        },
        description: {
            required: helpers.withMessage('Поле должно быть заполнено', required),
            minLength: helpers.withMessage('Поле должно быть не меньше 5 символов', minLength(5)),
        },
        images: {
            required: helpers.withMessage('Поле должно быть заполнено', required),
        }
    }
};

const v$ = useVuelidate(rules, { sku });

// const topLevelCategories = computed(() => {
//     return [{
//         id: null,
//         name: 'Выберите раздел',
//     }].concat(categoryTree.value.filter(el => !el.parent_id));
// });
//
// let lowLevelCategories = computed(() => {
//     let currentCategory = categoryTree.value.find(el => el.id === topLevelCategoryId.value)
//     if (currentCategory && currentCategory.children && currentCategory.children.length) {
//         return [{
//             id: null,
//             name: 'Выберите подраздел',
//         }].concat(currentCategory.children);
//     }
//     return [];
// });

let brands = computed(() => {
    return [{
        id: null,
        name: 'Выберите бренд',
    }].concat(allBrands.value);
});

const showAddingBrand = () => isShowAddingBrand.value = true;
const hideAddingBrand = () => isShowAddingBrand.value = false;


const saveBrand = async () => {
    await store.dispatch('brand/createItem', { name: newBrand.value });
    await store.dispatch('brand/loadAllBrands');
    hideAddingBrand();
};


const createNewSku = async () => {
    const validated = await v$.value.sku.$validate();
    if (validated) {
        sku.value.category_id = props.categoryId;
        const review = await store.dispatch('sku/createItem', sku.value);
        review.image = review.images.length ? review.images[0] : '';
        delete review.images;

        emit('setCurrentSku', review);
        v$.value.$reset();
    }
}

onMounted(async() => {
    await store.dispatch('brand/loadAllBrands');
})
</script>
<style scoped lang="scss">
@import '@/Admin/scss/form.scss';

.fill {
    width: 100%;
}
.back {
    display: flex;
    align-items: center;
    width: fit-content;
    margin-bottom: 8px;
    cursor: pointer;
    &:hover {
        color: #2c2c80;
    }
}
.brand {
    cursor: pointer;
    font-size: 18px;
    font-weight: 600;
    margin: 8px;
    display: block;
    &__block {
        position: relative;
        border: 1px dashed #d9d9d9;
        margin: 12px 0;
        border-radius: 4px;
        padding: 10px;
    }
    &__close {
        cursor: pointer;
        position: absolute;
        right: 0;
        top: 0;
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1;
        color: #000;
        text-shadow: 0 1px 0 #fff;
        opacity: 0.5;
        padding: 15px;
        border-style: none;
        background-color: transparent;
    }
}
</style>
