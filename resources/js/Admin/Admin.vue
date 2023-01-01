<template>
    <wrapper v-if="isAuth"/>
    <login v-else/>
    <notification/>
</template>

<script>
    import {mapActions, mapState} from "vuex";
    import wrapper from './components/wrapper/index.vue'
    import notification from './components/notification.vue'
    import login from './components/auth/login.vue'

    export default {
        name: "Admin",
        components: {
            wrapper,
            login,
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

<style scoped>
    .container {
        max-width:1400px;
        margin: 0 auto;
        height: 100%;
    }

    .menu {
        height: 100%;
        display:flex;
        align-items:center;
        justify-content: space-between;
    }

    a {
        text-decoration: none;
    }
    .navbar {
        height: 58px;
        background-color: #fff;
        margin-top: 20px;
        border-bottom: 2px solid #333;
        border-top: 1px solid #c4c4c4;
    }
    .header__logo {
        width: 250px;
        height: 30px;
    }
    .header_search_text {
        display: flex;
        align-items: center;
        justify-content: center;

    }
    .header_search_input {
        position: relative;
        height: 40px;
        width: 440px;

    }
    .header_search_input > input  {
        background-color: transparent;
        -webkit-appearance: textfield;
        border-radius: 5px;
        border: 1px solid #8c8c8c;
        line-height: 24px;
        padding: 0 21px 0 10px;
        font-size: 12px;
        min-width: 170px;
        width: 100%;
        height: 100%;
    }
    .header_search_input > input::placeholder {
        color: #999;
    }
    .header_search_button {
        background-color: transparent;
        border: none;
        position: absolute;
        top: 0;
        right: 0;
        width: 40px;
        height: 40px;
    }
    .dropdown-toggle {
        position: relative;
        color: #fff;
        background-color: #6c757d;
        padding: .375rem .75rem;
        border-radius: .25rem;
    }
    .drop {
        position: absolute;
        bottom: 0;
        right:0;
        transform: translateY(110%);
        z-index: 1099;
        min-width: 10rem;
        padding: .5rem 0;
        margin: .125rem 0 0;
        font-size: 1rem;
        color: #212529;
        text-align: left;
        list-style: none;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid rgba(0,0,0,.15);
        border-radius: .25rem;
    }

    .pop-enter-active,
    .pop-leave-active {
        transition: opacity 0.5s ease;
    }
    .pop-enter-from,
    .pop-leave-to {
        opacity: 0;
    }
    .main__block {
        height:100%;
        background-color: lightgray;
    }
    .menu__items {
        margin:0;
        list-style:none;
        display:flex;
        width:800px;
    }
    .menu__item {
        margin-left:20px;
    }
    .menu__item a {
        color: grey;
        text-decoration: none;
    }


</style>
