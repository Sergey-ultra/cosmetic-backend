<template>
    <modal
            v-model:isShowForm="isShowDeleteForm"
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

<script>
    import modal from './modal/modal.vue'
    import btn from './btn.vue'

    export default {
        name: "delete-form",
        components: {
            modal,
            btn,
        },
        props: {
            selectedName: String,
            isShowDeleteForm: {
                type:Boolean,
                default: false
            }
        },
    }
</script>

<style scoped lang="scss">
    .button {
        &:not(:last-child){
            margin-right: 15px;
        }
    }
</style>
