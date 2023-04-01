<template>
    <modal
        :width="80"
        v-model:isShowForm="isShowFormLocal"
    >
        <template v-slot:header>Спарсенная инфа</template>

        <h3>Сообщение</h3>
        <div>{{ preview.message }}</div>

        <div v-if="preview.links">
            <h3>Ссылки</h3>
            <div v-for="(link, linkIndex) in preview.links" :key="linkIndex">
                {{ linkIndex + 1 }} ссылка: {{ link }}
            </div>
        </div>

        <div v-if="preview.bodies">
            <h3>Страницы</h3>

            <expansion-panel class="form__item" v-for="(body, bodyIndex) in preview.bodies" :key="bodyIndex">
                <div>{{ bodyIndex + 1 }} страница</div>
                <div>
                    <span>Код ответа: {{ body.code }}</span>
                </div>

                <template v-slot:content>
                     <span>
                          <buttonComponent :size="'small'" @click="copyHtml(bodyIndex)">
                              Копировать
                          </buttonComponent>
                    </span>
                    <div>{{ body.content }}</div>
                </template>
            </expansion-panel>
        </div>

        <template v-slot:buttons>
            <buttonComponent :size="'small'" @click="$emit('update:isShowForm', false)">
                Отмена
            </buttonComponent>
        </template>
    </modal>
</template>

<script>
import modal from "../../../components/modal/modal.vue"
import {mapState} from "vuex";
import expansionPanel from "../../../components/expansion-panel.vue";
import buttonComponent from "../../../components/button-component.vue"

export default {
    name: "preview-links-modal",
    components: {
        modal,
        expansionPanel,
        buttonComponent
    },
    props: {
        isShowForm: {
            type: Boolean,
            default: false
        }
    },
    computed: {
        ...mapState('linkParser',['preview']),
        isShowFormLocal: {
            get() {
                return this.isShowForm;
            },
            set(value) {
                this.$emit('update:isShowForm', value)
            }
        }
    },
    methods: {
        copyHtml(index) {
            try {
                navigator.clipboard.writeText(this.preview.bodies[index].content)
            } catch (e) {
                throw e
            }
        }
    }
}
</script>

<style scoped lang="scss">
.flex {
    align-items: center;
    display: flex;
    flex-wrap: wrap;
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
</style>
