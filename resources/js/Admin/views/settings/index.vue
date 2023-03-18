<template>
    <button type="button" @click="toggleIsRequiredEmailVerification" class="button button-settings">
        <span>{{ buttonText }}</span>
        <loader :color="'#fff'" class="loader" v-if="isSetting"/>
    </button>
</template>

<script>
    import {mapActions, mapState} from "vuex";
    import loader from "../../components/loader.vue"

    export default {
        name: "settings",
        components:{
            loader
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
    .button {
        transition: all 0.12s ease-out;
        background-color: #fc0;
        -webkit-appearance: none;
        border: 0;
        border-radius: 4px;
        position: relative;
        line-height: 36px;
        height: 36px;
        padding: 0 16px;
        margin-top: 16px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        color: inherit;
        text-align: center;
    }
    .loader {
        position: absolute;
        right: 0;
    }
</style>
