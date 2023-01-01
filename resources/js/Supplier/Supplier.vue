<template>
    <wrapper v-if="isAuth"/>
    <auth v-else/>
    <notification/>
</template>

<script>
    import {mapActions, mapState} from "vuex";
    import wrapper from './components/wrapper/index.vue'
    import notification from './components/notification.vue'
    import auth from './components/auth/index.vue'

    export default {
        name: "Admin",
        components:{
            wrapper,
            auth,
            notification
        },
        data () {
            return {

            }
        },
        computed: {
            ...mapState('auth', ['isAuth'])
        },
        created() {
            this.checkAuth()

        },
        methods: {
            ...mapActions('auth', ['checkAuth']),
        }
    }
</script>

<style scoped lang="sccs">

</style>
