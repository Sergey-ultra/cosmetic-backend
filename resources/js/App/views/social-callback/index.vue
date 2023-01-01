<template>
    Wait! Login via Services.
</template>

<script setup>
    import { onMounted } from 'vue';
    import {useAuthStore} from "../../store/auth";
    import {useRoute, useRouter} from "vue-router";
    const authStore = useAuthStore();

    const route = useRoute();
    const router = useRouter();

    onMounted(() => {
        const {token, user_name, avatar} = route.query
        authStore.loginViaSocialServices({ user_name, token, avatar })
        router.push('/')
    });
</script>

<style scoped>

</style>