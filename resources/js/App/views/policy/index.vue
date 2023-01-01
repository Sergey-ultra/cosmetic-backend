<template>
    <h2 class="title">{{ $route.meta.title }}</h2>
    <div class="block">
        Данная Политика конфиденциальности регламентирует обязанности веб-сайта https://smart-beautifull.ru касательно неразглашения и обеспечения защиты личных данных посетителей веб-сайта, которые предоставляются Посетителями при использовании ресурса, в том числе при регистрации и оформлении заказа.
        Пользователь, посещающий веб-сайт smart-beautifull.ru.ru, автоматически соглашается с данной Политикой конфиденциальности и условиями использования предоставляемых им личных данных.
        В противном случае Пользователю необходимо отказаться от использования веб-сайта smart-beautifull.ru.
    </div>
</template>



<style lang="scss" scoped>
    .block {
        margin: 20px 0;
        display: flex;
        width: 100%;
        line-height: 24px;
        flex-flow: column nowrap;
        justify-content: flex-start;
        align-items: center;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 6px 40px 0 rgba(56,69,94,.07);
        padding: 54px 32px;
    }
</style>