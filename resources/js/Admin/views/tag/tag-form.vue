<template>
    <modal
        v-model:isShowForm="isShowForm"
        :width="60"
    >
        <template v-slot:header>
            {{ selectedTagId ? 'Редактирование тега' : 'Создание нового тега' }}
        </template>
        <form class="form">
            <div class="input__group">
                <div class="input__item">Name</div>
                <div class="input__item">
                    <input v-model="editedTag.tag" type="text" class="form__control  input">
                </div>
            </div>

            <div class="input__group">
                <div class="input__item">Описание</div>
                <div class="input__item">
                    <textarea v-model="editedTag.description"  class="form__control  textarea"></textarea>
                </div>
            </div>

            <div class="input__group">
                <div class="input__item">Родительский тег</div>
                <div class="input__item">
                    <select v-model="editedTag.parent_id" type="text" class="form__control input">
                        <option
                            v-for="option in availableTagsLocal"
                            :key="option.id"
                            :value="option.id"
                        >
                            {{ option.tag }}
                        </option>
                    </select>
                </div>
            </div>
        </form>
        <template v-slot:buttons>
            <btn class="button" @click="$emit('update:isShowForm', false)">Отмена</btn>
            <btn  @click="save">{{ selectedTagId ? 'Сохранить' : 'Создать' }}</btn>
        </template>
    </modal>
</template>

<script>
import btn from "../../components/btn.vue";
import modal from "../../components/modal/modal.vue";
import {mapActions, mapGetters, mapState} from "vuex";

export default {
    name: "tag-form",
    components:{
        btn,
        modal
    },
    props: {
        selectedTagId: Number,
        isShowForm: Boolean
    },
    data() {
        return {
            editedTag: {
                parent_id: null
            }
        }
    },
    computed: {
        ...mapState('tag', ['currentTag']),
        ...mapGetters('tag', ['availableTagsLocal']),
    },
    async created() {
        this.loadAvailableTags();
        if (this.selectedTagId) {
            await this.loadItem(this.selectedTagId)
            this.initEditedObject()
        }
    },
    watch:{
        async selectedTagId(value) {
            if (value) {
                await this.loadItem(value)
                this.initEditedObject()
            } else {
                this.editedTag = {
                    parent_id: null
                }
            }
        }
    },
    methods:{
        ...mapActions('tag', ['loadAvailableTags', 'loadItem', 'createItem', 'updateItem']),
        initEditedObject() {
            this.editedTag = {...this.currentTag }
        },
        async save() {
            if (!this.selectedTagId) {
                await this.createItem(this.editedTag)
            }  else {
                await this.updateItem(this.editedTag)
            }
            this.$emit('update:isShowForm', false)
        }
    }
}
</script>

<style scoped>

</style>
