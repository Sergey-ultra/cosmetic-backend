<template>
   <div class="main">
      <img src="/storage/icons/401.svg">
      <h2>{{ text }}</h2>
      <button class="btn">
         <router-link :to="route.query.from">Назад</router-link>
      </button>
      <button class="btn btn-login" @click="authStore.setIsShowAuthModal(true)">
         <span>Войти</span>
      </button>
   </div>
</template>

<script setup>
    import {useAuthStore} from "../../store/auth";
    import { useRoute } from 'vue-router'
    import { computed } from "vue";

    const route = useRoute();
    const text = computed(() => {
        switch (route.query.to) {
            case 'add-photos':
                return 'Войдите, чтобы оставить фото о товаре'
            case 'add-review':
                return 'Войдите, чтобы оставить отзыв о товаре'
            default:
                return 'Вы не авторизованы'
        }
    });
    const authStore = useAuthStore();
</script>

<style scoped lang="scss">
   .main {
      padding: 20px;
      min-height: 400px;
      background-color: #fff;
      border-radius: 8px;
      width: 100%;
      margin: 48px auto;
      text-align: center;
   }
   .btn {
      transition: all .12s ease-out;
      background-color: #e8e8e8;
      padding: 0 16px;
      height: 44px;
      border: 0;
      border-radius: 4px;
      position: relative;
      line-height: 36px;
      &:not(:last-child) {
         margin-right: 10px;
      }
      & a {
         text-decoration: none;
         color: #2b2b2b;
         font-size: 16px;
         font-weight: 700;
         line-height: 44px;
      }
      &-login {
         background-color: #fc0;
         color: #2b2b2b;
         font-size: 16px;
         font-weight: 700;
         line-height: 44px;
      }
   }
</style>