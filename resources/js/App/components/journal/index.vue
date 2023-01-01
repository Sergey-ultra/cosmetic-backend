<template>
    <div class="journal skeleton-loader" v-if="isLoadingArticles"></div>

    <div class="journal" v-else-if="articles.length">
        <div class="journal__title">Новости и секреты красоты</div>
        <div class="journal__wrapper">
            <div v-for="article in articles" :key="article.id" class="journal__item">
                <router-link :to="{ name: 'article',  params: { id: article.id } }">
                    <div class="journal__image">
                        <img :src="article.image" :alt="article.image">
                        <div class="journal__badge">
                            <span class="journal__tag" :style="{backgroundColor: getBackgroundColor(article.tag) }">{{ getArticleTag(article.tag) }}</span>
                        </div>
                    </div>
                    <div class="journal__main">
                        {{ article.title }}
                    </div>
                    <div class="journal__preview">
                         {{article.preview}}
                    </div>
                    <div class="journal__user">
                        <div class="journal__avatar" v-if="article.user_avatar">
                            <img :src="article.user_avatar" :alt="article.user_avatar">
                        </div>
                        <div class="journal__name">
                            {{ article.user_name }}
                        </div>
                        <div class="journal__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" data-tid="9bd43b8c">
                                <path fill="#999" d="M12 17c3.356 0 6.549-1.81 8.96-5.01C18.527 8.794 15.341 7 12 7c-3.356 0-6.549 1.81-8.96 5.01C5.473 15.206 8.659 17 12 17zm0-11c3.816 0 7.299 2.097 9.85 5.51.2.29.2.668 0 .958C19.3 15.903 15.816 18 12 18s-7.299-2.097-9.85-5.51c-.2-.29-.2-.668 0-.958C4.7 8.097 8.184 6 12 6zm-3.5 5.994c.458.349 1.04.541 1.671.5 1.224-.073 2.252-1.1 2.326-2.323A2.568 2.568 0 0 0 11.99 8.5a3.512 3.512 0 0 1 3.503 3.73 3.494 3.494 0 0 1-3.263 3.263 3.512 3.512 0 0 1-3.73-3.499z"></path>
                            </svg>
                        </div>
                        <div class="journal__viewsCount">{{ article.views_count  }}</div>
                    </div>
                </router-link>
            </div>
        </div>
    </div>

</template>

<script setup>
    import {useArticleStore} from "../../store/article";
    import {storeToRefs} from "pinia";
    import {onMounted} from "vue";

    const articleStore = useArticleStore();
    const { articles, isLoadingArticles } = storeToRefs(articleStore);

    const getArticleTag = value => {
        switch (value) {
            case 'review':
                return 'Обзор';
            case 'compilation':
                return 'Подборка';
            case 'opinion':
                return 'Мнение эксперта';
        }
    };

    const getBackgroundColor = value => {
        switch (value) {
            case 'review':
                return '#ffa300';
            case 'compilation':
                return '#673ab7';
            case 'opinion':
                return '#ff4500';
        }
    };

    onMounted(() => articleStore.loadArticles());
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
    a {
        text-decoration: none;
    }
    .journal {
        width: 100%;
        min-height:300px;
        &__title {
            font-weight: 500;
            line-height: 32px;
            font-size: 26px;
            color: #222;
            padding-bottom: 20px;
        }
        &__wrapper {
            height: 360px;
            width: 100%;
            border-radius: 8px;
            background-color: #fff;
            overflow:hidden;
            padding: 20px;
            display: flex;

        }
        &__item {
            height: 100%;
            width: 296px;
            &:not(:last-child) {
                margin-right: 15px;
            }
            & a {
                height: 100%;
                display: flex;
                flex-direction: column;
            }
        }
        &__image {
            position: relative;
            width: 100%;
            overflow: hidden;
            padding-top: 62.5%;
            & img {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-position: 50%;
                background-repeat: no-repeat;
                object-fit:cover;
            }
        }
        &__badge {
            position: absolute;
            bottom:0;
            left: 0;
            z-index:2;
        }
        &__tag {
            color: #fff;
            display: inline-block;
            padding: 3px 6px 2px;
            font-size: 11px;
            line-height: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.1px;
        }
        &__main {
            font-weight: 700;
            font-size: 20px;
            line-height: 24px;
            color: #222;
            margin-top: 4px;
        }
        &__preview {
            font-weight: 400;
            font-size: 16px;
            line-height: 20px;
            max-height:60px;
            margin-top: 4px;
            margin-bottom: 10px;
            color: #999;
            overflow: hidden;
            text-overflow: ellipsis;
            position: relative;
            &:after {
                background: linear-gradient(rgba(255, 255, 255, 0), #fff);
                content: "";
                height: 30px;
                left: 0;
                position: absolute;
                top: 44px;
                width: 100%;
            }
        }

        &__user {
            margin-top: auto;
            color: #999;
            font-size: 16px;
            line-height: 22px;
            white-space: nowrap;
            display: flex;
            flex-wrap:wrap;
            align-items: center;
        }
        &__avatar {
            flex-shrink: 0;
            height: 24px;
            width: 24px;
            margin-right: 10px;
            & img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                border-radius: 50%;
            }
        }
        &__name {
            padding-right: 16px;
        }
        &__icon {
            width: 20px;
            height: 20px;
        }
        &__viewsCount {
            margin-left:2px;
            color: #999;
            font-family: YS Text,sans-serif;
            font-weight: 400;
            font-style: normal;
            font-stretch: normal;
            font-size: 14px;
            line-height: 20px;
            white-space: nowrap;
        }
    }

    @media (max-width: 1000px) {
        .journal__item {
            width: 250px;
        }
    }

    @media (max-width: 700px) {
        .journal {
            &__wrapper {
                padding:15px;
            }
            &__item {
                width: 200px;
                height: 200px;
            }
        }
    }
    @media (max-width: 400px) {
        .journal {
            &__wrapper {
                padding:10px;
            }
            &__item {
                height: 150px;
                width: 180px;

            }
        }
    }
</style>