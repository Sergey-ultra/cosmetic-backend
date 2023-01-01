<template>
    <div>
        <btn @click="showForm(null)">Добавить</btn>
        <div class="tag">
            <tagItem
                @showForm="showForm"
                @showDeleteForm="showDeleteForm"
                :tag="tag"
                :key="tag.id"
                v-for="tag in tagTree"
            />
        </div>

        <tag-form
            :selectedTagId="selectedTagId"
            v-model:isShowForm="isShowForm"
        />
        <delete-form
            :selectedName="selectedName"
            v-if="isShowDeleteForm"
            v-model:isShowDeleteForm="isShowDeleteForm"
            @delete="deleteTag"
        />
    </div>
</template>

<script>
import btn from "../../components/btn.vue";
import tagItem from "../../components/tag-item.vue";
import deleteForm from '../../components/delete-form.vue'
import tagForm from "./tag-form.vue";
import {mapActions, mapState} from "vuex";

export default {
    name: "index",
    components: {
        tagItem,
        btn,
        tagForm,
        deleteForm
    },
    data() {
        return {
            isShowForm: false,
            selectedTagId: null,
            selectedName: null,
            isShowDeleteForm:false,
        }
    },
    computed:{
        ...mapState('tag', ['isLoadingTagTree','tagTree']),
    },
    created() {
        this.loadTagTree();
    },
    methods: {
        ...mapActions('tag', ['loadTagTree', 'createItem', 'deleteItem']),
        showForm(id) {
            this.isShowForm = true
            this.selectedTagId = id
        },
        showDeleteForm(item) {
            this.isShowDeleteForm = true
            this.selectedTagId = item.id
            this.selectedName = item.name
        },
        deleteTag(){
            this.deleteItem(this.selectedTagId)
            this.isShowDeleteForm = false
        }
    }
}
</script>

<style scoped lang="scss">
.tag {
    border-radius: 8px;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.16);
    background-color: #fff;
    overflow: hidden;
    padding: 20px;
    margin: 20px 0;
    border: 1px solid #999999;
    max-width: 800px;
}
</style>
