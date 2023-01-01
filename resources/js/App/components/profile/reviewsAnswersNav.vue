<template>
    <nav class="nav">
        <div class="btn" :class="{'active': $route.name ===  'myReviews'}">
            <router-link :to="{ name: 'myReviews'}">Отзывы</router-link>
        </div>
        <div class="btn" :class="{'active': $route.name ===  'myComments'}">
            <router-link :to="{ name: 'myComments'}">Комментарии</router-link>
        </div>
        <div class="btn" :class="{'active': $route.name ===  'myAnswers'}">
            <router-link :to="{ name: 'myAnswers'}">Ответы</router-link>
        </div>
    </nav>
</template>

<style lang="scss" scoped>
    .nav {
        display: flex;
        white-space: nowrap;
        padding-bottom: 13px;
    }
    .btn {
        border-radius: 4px;
        display: flex;
        align-items: center;
        text-align: center;
        margin-right: 0;
        background-color: #e8e8e8;
        &:not(:first-child){
            margin-left: 12px;
        }
        & a {
            padding: 10px 16px;
            text-transform: capitalize;
            letter-spacing: .25px;
            font-size: 14px;
            line-height: 16px;
            color: #222;
            display: block;
            font-weight: 700;
            text-decoration: none;
            user-select: none;
        }
    }
    .active {
        background-color: #666;
        & a {
            color: #fff;
        }
    }
</style>