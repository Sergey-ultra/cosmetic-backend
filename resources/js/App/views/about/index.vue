<template>
    <h2 class="title">{{ route.meta.title }}</h2>
    <div class="block">
        <div class="row">
            <div class="row__item">
                <h4>Отзывы и предложения по работе сайта Smart-Beautiful.ru</h4>
<!--                <blockquote>-->
                    <p>Smart-Beautiful - это сервис сравнения цен на косметических товаров.</p>

                    <p>Сайт создан для удобного сравнения цен на косметику в популярных интернет-магазинах России.</p>

                    <p>Мы не занимаемся непосредственной продажей и коммерческой деятельностью.
                    По вопросам работы сайта можно написать на почту admin@smart-beautiful.ru</p>
<!--                </blockquote>-->

                <div>
                    Если у вас есть предложение по работе сайта, или вы нашли ошибку,
                    Напишите отзыв ниже в комментариях.
                </div>
            </div>
        </div>

        <h4>Комментарии пользователей</h4>
        <form class="form" @submit.prevent="sendComment">
            <div>Добавить сообщение</div>

            <textarea class="textarea margin-top" placeholder="Комментарий"></textarea>

            <div class="form__row">
                <input class="form__item input" type="text" placeholder="Ваше имя">
                <button class="form__item button" type="submit">Отправить</button>
            </div>

        </form>

    </div>
</template>

<script setup>
const sendComment = () => {

}
</script>

<style lang="scss" scoped>
    .margin-top {
        margin-top: 15px;
    }
    .textarea {
        width: 100%;
        resize: vertical;
        outline: none;
        overflow: visible;
        transition: background-color 0.3s ease 0s, border-color 0.3s ease 0s;
        border: 1px solid transparent;
        border-radius: 8px;
        padding: 8px;
        background-color: rgb(240, 242, 252);
        &:hover {
            border-color: rgb(192, 201, 240);
            transition: border-color 0.3s ease 0s;
        }
        &:focus {
            background-color: white;
            border-color: rgb(59, 87, 208);
            transition: background-color 0.3s ease 0s, border-color 0.3s ease 0s;
        }
    }
    .input {
        height: 38px;
        width: 200px;
        outline: #000 none medium;
        overflow: visible;
        transition: background-color 0.3s ease 0s, border-color 0.3s ease 0s;
        border: 1px solid transparent;
        border-radius: 8px;
        padding: 8px;
        background-color: rgb(240, 242, 252);
        &:hover {
            border-color: rgb(192, 201, 240);
            transition: border-color 0.3s ease 0s;
        }
        &:focus {
            background-color: white;
            border-color: rgb(59, 87, 208);
            transition: background-color 0.3s ease 0s, border-color 0.3s ease 0s;
        }
    }

    .button {
        height: 38px;
        background-color: #fc0;
        border: none;
        color: #333;
        border-radius: 8px;
        transition: background-color 0.3s ease 0s, border-color 0.3s ease 0s;
        padding: 8px 30px;
        font-size: 18px;
        &:hover {
            background-color: #f5c423;
        }
    }
    .block {
        width: 100%;
        margin: 20px 0;
        display: flex;
        flex-flow: column nowrap;
        justify-content: flex-start;
        align-items: center;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 6px 40px 0 rgba(56,69,94,.07);
        padding: 54px 32px;
    }
    .row {
        display: flex;
        flex-flow: column wrap;
        &__item {
            width: 50%;
            text-align: justify;
        }
    }

    .form {
        width: 100%;
        &__row {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        &__item {
            margin-top: 15px;
        }
    }

    @media (max-width: 700px) {
        .row {
            &__item {
                width: 100%;
            }
        }
        .form {
            &__item {
                width: 100%;
            }
        }
    }
</style>