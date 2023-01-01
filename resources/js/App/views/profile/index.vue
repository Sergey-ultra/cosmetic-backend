<template>
    <div class="container">
        <h2 class="title">{{ $route.meta.title }}</h2>
        <div class="profile">
            <div class="profile__sidebar">
                <div class="profile__personal">
                    <div class="">
                        <router-link :to="{ name: 'editProfile'}">
                            <div class="profile__avatar">
                                <img v-if="userAvatar" :src="userAvatar" :alt="userAvatar">
                                <svg  v-else xmlns="http://www.w3.org/2000/svg" width="120" height="120" fill-rule="evenodd" clip-rule="evenodd" image-rendering="optimizeQuality" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" viewBox="0 0 32 32"><circle cx="16" cy="16" r="16" fill="#ebebeb"/><path fill="#ccc" d="M16 18.85c6.27 0 11.36 3.46 11.36 7.73 0 .25-.02.5-.05.74C24.41 30.21 20.42 32 16 32c-4.42 0-8.41-1.79-11.31-4.68-.03-.24-.05-.49-.05-.74 0-4.27 5.09-7.73 11.36-7.73zm0-12.66c2.95 0 5.34 2.39 5.34 5.34 0 2.95-2.39 5.34-5.34 5.34-2.95 0-5.34-2.39-5.34-5.34 0-2.95 2.39-5.34 5.34-5.34z"/></svg>
                            </div>
                        </router-link>

                        <div class="profile__username">{{ userName }}</div>
                        <div class="profile__description">
                            <div>{{ userInfo.review_count ? userInfo.review_count : 0 }} отзывов</div>
                            <div>0 ответов</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="profile__main">
                <router-view></router-view>
            </div>
        </div>
    </div>

</template>

<script setup>
    import { onMounted  } from 'vue';
    import { storeToRefs } from "pinia";
    import {useAuthStore} from "../../store/auth";
    import {useUserStore} from "../../store/user";

    const authStore = useAuthStore();
    const userStore = useUserStore();
    const { userName, userAvatar } = storeToRefs(authStore);
    const { userInfo } = storeToRefs(userStore);

    onMounted(async () => {
        await userStore.loadUserInfo();
    })
</script>

<style lang="scss" scoped>
    .profile {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        margin-top: 20px;
        border-radius: 8px;
        &__avatar {
            height: 120px;
            width: 120px;
            border-radius: 50%;
            margin: 20px auto;
            display:flex;
            justify-content: center;
            align-items: center;
            & img {
                border-radius: 50%;
                width: 100%;
                height: 100%;
                object-fit:cover;
            }
        }
        &__username {
            font-size: 28px;
            line-height: 36px;
            font-weight: 500;
        }
        &__description {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            color: #222;
            font-size: 14px;
            letter-spacing: .3px;
            margin-bottom: 36px;
            & > :not(:last-child) {
                margin-right: 6px;
                &::after {
                    content: "";
                    width: 3px;
                    height: 3px;
                    vertical-align: middle;
                    border-radius: 50%;
                    margin-left: 5px;
                    display: inline-block;
                    background-color: #999;
                }
            }
        }
        &__main {
            width: 80%;
            min-height:300px;
        }
        &__sidebar {
            display: flex;
            flex-direction: column;
            width: 18%;
            border-radius: 8px;
            padding:20px;
            background-color: #ffeaf2;
            max-height: 350px;
        }
    }
    @media (max-width: 900px) {
        .profile {
            &__sidebar,
            &__main {
                width: 100%;
                margin-bottom: 20px;
            }
            &__sidebar {
                flex-direction: row;
                justify-content: center;
            }
            &__avatar {
                margin: 0;
                margin-right: 20px;
                display: flex;
                justify-content: center;
                align-items: center;
                & img {
                    max-width: 100%;
                    max-height: 100%;
                }
            }
            &__personal {
                align-self: center;
            }
        }
    }

</style>
