<template>
    <modal
            v-model:isShowForm="isShowForm"
            :width="60"
    >
        <template v-slot:header>
           {{selectedCountryId ? 'Редактирование страны' : 'Создание новой страны'}}
        </template>

        <form @submit.prevent="">
            <div class="input-group">
                <label>
                    Имя
                    <input v-model="editedCountry.name" type="text" class="form-control">
                </label>
            </div>
        </form>

        <template v-slot:buttons>
            <btn class="button" @click="$emit('update:isShowForm', false)">Отмена</btn>
            <btn @click="save">{{ selectedCountryId ? 'Редактировать' : 'Создать' }}</btn>
        </template>
    </modal>
</template>

<script>
    import {mapActions, mapState} from "vuex";
    import modal from '../../components/modal/modal.vue'
    import btn from '../../components/btn.vue'

    export default {
        name: "country-form",
        components: {
            btn,
            modal
        },
        props: {
            selectedCountryId: Number,
            isShowForm: {
                type:Boolean
            }
        },
        data() {
            return {
                editedCountry: {},
            }
        },
        computed: {
            ...mapState('country', ['loadedCountry']),
        },
        watch: {
            isShowForm(value) {
                console.log(value)
            },
            async selectedCountryId(value) {
                if (value) {
                    await this.loadItem(value)
                    this.initEditedObject()
                } else {
                    this.editedCountry = {}
                }
            }
        },
        async created() {
            if (this.selectedCountryId) {
                await this.loadItem(this.selectedCountryId)
                this.initEditedObject()
            }
        },
        methods: {
            ...mapActions('country', ['loadItem', 'createItem', 'updateItem']),
            initEditedObject() {
                this.editedCountry = {...this.loadedCountry }
            },
            async save() {
                if (!this.selectedCountryId) {
                    await this.createItem(this.editedCountry)
                }  else {
                    await this.updateItem(this.editedCountry)
                }
                this.$emit('update:isShowForm', false)
            }
        }
    }
</script>

<style scoped lang="scss">
    .button {
        &:not(:last-child) {
            margin-right: 15px;
        }
    }
</style>
