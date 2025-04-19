<template>
    <buttonComponent
        @click="toggleIsRequiredEmailVerification"
        :color="'yellow'"
        class="button-settings"
    >
        <span>{{ buttonText }}</span>
    </buttonComponent>
</template>

<script>
    import {mapActions, mapState} from "vuex";
    import buttonComponent from "../../components/button-component.vue"

    export default {
        name: "settings",
        components:{
            buttonComponent
        },
        computed: {
            ...mapState('settings', ['isSetting', 'isRequiredEmailVerification']),
            buttonText() {
                return typeof this.isRequiredEmailVerification == "boolean" && this.isRequiredEmailVerification
                    ? 'Снять проверку email при регистрации'
                    : 'Добавить проверку email при регистрации';
            }
        },
        created() {
            this.loadIsRequiredEmailVerification();
        },
        methods: {
            ...mapActions('settings', ['setIsRequiredEmailVerification', 'loadIsRequiredEmailVerification']),
            toggleIsRequiredEmailVerification() {
                this.setIsRequiredEmailVerification(!this.isRequiredEmailVerification)
            }
        }
    }
</script>

<style lang="scss" scoped>
    .button-settings {
        margin-top: 16px;
    }
</style>
