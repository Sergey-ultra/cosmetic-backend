<template>
    <modal
            v-model:isShowModal="isShowDeleteFormLocal"
            :width="30"
    >
        <template v-slot:header>
           <h4>Вы действительно хотите удалить {{ selectedName }}?</h4>
        </template>

        <template v-slot:buttons>
            <btn class="button" @click="$emit('update:isShowDeleteForm', false)">Отмена</btn>
            <btn  @click="$emit('delete')">Удалить</btn>
        </template>
    </modal>
</template>

<script setup>
    import modal from './modal'
    import btn from './btn'
    import {defineProps, defineEmits, computed} from "vue";

    const emit = defineEmits(['update:isShowDeleteForm']);
    const props = defineProps({
        selectedName: String,
        isShowDeleteForm: {
            type: Boolean,
            default: false
        }
    });

    const isShowDeleteFormLocal = computed({
        get() {
            return props.isShowDeleteForm;
        },
        set(value) {
           emit('update:isShowDeleteForm', value);
        }
    });
</script>

<style scoped lang="scss">
    .button {
        &:not(:last-child){
            margin-right: 15px;
        }
    }
</style>