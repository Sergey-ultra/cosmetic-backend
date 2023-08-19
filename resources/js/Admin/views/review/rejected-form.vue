<template>
    <modal
        v-model:isShowForm="isShowRejectModal"
        :width="50">
        <template v-slot:header>
            Отклонение отзыва {{ loadedReview.title ?? '' }}
        </template>
        <form @submit.prevent="rejectReview">
            <div class="input-group reason">
                <selectComponent
                    v-model="reasonIds"
                    :color="'white'"
                    :multiple="true"
                    :placeholder="'Выберите причины отказа'"
                    :items="rejectedReasons"
                    :item-title="'reason'"
                    :item-value="'id'"/>
            </div>

            <btn type="submit">Отказать</btn>
        </form>
    </modal>
</template>
<script>
import {mapActions, mapState} from "vuex";
import btn from "../../components/btn.vue";
import modal from "../../components/modal/modal.vue";
import selectComponent from "../../components/select-component-extended.vue";

export default {
    components: {
        modal,
        btn,
        selectComponent,
    },
    data() {
        return {
            reasonIds: [],
        }
    },
    props: {
        rejectedReviewId: Number,
        isShowRejectModal: {
            type:Boolean,
            default: false
        }
    },
    computed: {
        ...mapState('review', ['loadedReview', 'rejectedReasons']),
    },
    async created() {
        this.loadRejectedReasons();
        if (this.rejectedReviewId) {
            await this.loadItem(this.rejectedReviewId)
        }
    },
    methods: {
        ...mapActions('review', ['loadItem', 'reject', 'loadRejectedReasons']),
        rejectReview() {
            this.reject({
                id: this.rejectedReviewId,
                reason_ids: this.reasonIds,
            })
        },
    },
}
</script>
<style scoped lang="scss">
.reason {
    margin-bottom: 100px;
}
</style>
