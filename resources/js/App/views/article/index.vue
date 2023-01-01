<template>
    <div v-if="isLoadingCurrentArticle" class="skeleton-loader"></div>


    <div v-else-if="currentArticle !== null" class="article">
        <div class="article__title">{{ currentArticle.title }}</div>
        <div class="article__preview">{{ currentArticle.preview }}</div>
        <div class="article__meta">
            <div class="article__user">
                <div class="article__userImg">
                    <img :src="currentArticle.user_avatar" :alt="currentArticle.user_name">
                </div>
                <div class="article__userInfo">
                    <div class="article__userName">
                        <span>
                            {{ currentArticle.user_name }}
                        </span>
                    </div>

                </div>
            </div>
            <div class="article__info">
                <div class="article__infoItem">{{ currentArticle.created_at }}</div>
                <div class="article__infoItem">
                    <span class="article__viewsIcon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" data-tid="9bd43b8c">
                            <path fill="#999" d="M12 17c3.356 0 6.549-1.81 8.96-5.01C18.527 8.794 15.341 7 12 7c-3.356 0-6.549 1.81-8.96 5.01C5.473 15.206 8.659 17 12 17zm0-11c3.816 0 7.299 2.097 9.85 5.51.2.29.2.668 0 .958C19.3 15.903 15.816 18 12 18s-7.299-2.097-9.85-5.51c-.2-.29-.2-.668 0-.958C4.7 8.097 8.184 6 12 6zm-3.5 5.994c.458.349 1.04.541 1.671.5 1.224-.073 2.252-1.1 2.326-2.323A2.568 2.568 0 0 0 11.99 8.5a3.512 3.512 0 0 1 3.503 3.73 3.494 3.494 0 0 1-3.263 3.263 3.512 3.512 0 0 1-3.73-3.499z"></path>
                        </svg>
                    </span>
                     {{ currentArticle.views_count }}
                </div>
            </div>
        </div>

        <div class="article__img">
            <img :src="currentArticle.image" :alt="currentArticle.image">
        </div>
        <div class="article__body" v-html="currentArticle.body"></div>
    </div>
</template>

<script setup>
    import {useArticleStore} from "../../store/article";
    import { watch,  onMounted } from 'vue';
    import { storeToRefs } from "pinia";
    import {useRoute} from 'vue-router'

    const route =  useRoute();
    const articleStore = useArticleStore();
    const { currentArticle, isLoadingCurrentArticle } = storeToRefs(articleStore);


    watch(
        currentArticle,
        (value) => {
            document.title = `Статья ${value.title} Smart-Beautiful - агрегатор цен косметических товаров`;
        }
    );

    onMounted(() => {
        if (route.params.id) {
            articleStore.loadCurrentArticle(route.params.id)
        }
    });
</script>

<style lang="scss" scoped>
    @keyframes load {
        from {
            left: -150px;
        }
        to {
            left: 100%;
        }
    }

    .skeleton-loader {
        position:relative;
        overflow: hidden;
        &:before {
            content: '';
            display: block;
            position: absolute;
            left: -150px;
            top: 0;
            height: 100%;
            width: 150px;
            background: linear-gradient(to right, transparent 0%, #E8E8E8 50%, transparent 100%);
            animation: load 1s cubic-bezier(0.4, 0.0, 0.2, 1);
        }
    }

    .article {
        width: 100%;
        background-color: #fff;
        padding:25px;
        border-radius: 8px;
        margin-top: 60px;
        &__title {
            font-weight: 500;
            font-size: 45px;
            line-height: 56px;
            margin: 16px 0 0;
            color: #222;
            max-width: 920px;
        }
        &__preview {
            font-weight: 400;
            font-size: 28px;
            line-height: 36px;
            margin-top: 8px;
            letter-spacing: .2px;
            color: #999;
        }
        &__meta {
            margin-top: 24px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            flex-wrap:wrap;
        }
        &__user {
            flex: 1 0 450px;
            display: flex;
            align-items: center;
        }
        &__userImg {
            flex: 0 0 62px;
            margin-right: 16px;
            width: 62px;
            height: 62px;
            border-radius: 50%;
            overflow:hidden;
            & img {
                object-fit: cover;
                width: 100%;
                height: 100%;
            }
        }
        &__userInfo {

        }
        &__userName {
            color: #222;
            font-weight: 600;
            font-style: normal;
            font-stretch: normal;
            font-size: 16px;
            line-height: 24px;
        }
        &__firstLetter {
            color: red;
            font-style: normal;
        }
        &__info {
            padding: 9px 0;
            display: flex;
            justify-content: flex-start;
            align-items: center;
        }
        &__infoItem {
            color: #999;
            font-family: YS Text,sans-serif;
            font-weight: 400;
            font-style: normal;
            font-stretch: normal;
            font-size: 14px;
            line-height: 20px;
            white-space: nowrap;
            &:not(:last-child) {
                padding-right: 16px;
            }
        }


        &__viewsIcon {
            display:inline-block;
            width: 20px;
            height: 20px;
            vertical-align: top;
        }


        &__img {
            margin-top: 24px;
            height: 580px;
            width: 100%;

            & img {
                height: 100%;
                width: 100%;
                object-fit:cover;
            }
        }

        &__body {
            font-size: 22px;
            line-height: 1.5;
        }
        & .image {
            width: 400px;
            height: 300px;
            object-fit: cover;
        }
    }
</style>