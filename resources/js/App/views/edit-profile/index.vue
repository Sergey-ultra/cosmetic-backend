<template>
    <h2 class="title">{{ $route.meta.title }}</h2>
    <div class="edit">
        <div class="edit__item edit__photo">
            <img v-if="userAvatar"  :src="userAvatar" :alt="userAvatar">
            <img v-else src="/storage/icons/user-mf.svg" alt="user-mf.svg">

            <fa class="edit__photo-icon icon" icon="camera"></fa>
            <input @change="loadImages($event)" type="file" id="formFileMultiple" multiple accept="image/jpeg, image/png">

        </div>
        <form class="edit__item edit__form">
            <div class="edit__form-row">
                <label for="user_name">Ваше имя</label>
                <input id="user_name" class="input" type="text" v-model="editedUserInfo.name">
            </div>
            <div class="edit__form-row">
                <span class="label">Пол</span>
                <div>
                    <label for="male">М</label>
                    <input v-model="editedUserInfo.sex" id="male" type="radio" value=0 class="">
                </div>
                <div>
                    <label for="female">Ж</label>
                    <input v-model="editedUserInfo.sex" id="female" type="radio" value=1 class="">
                </div>
            </div>
            <div class="edit__form-row">
                <label for="year">Год рождения</label>
                <select name="" id="year" class="select" v-model="editedUserInfo.birthday_year">
                    <option value="null">Выберите год рождения</option>
                    <option v-for="year  in years" :value="year">{{ year }}</option>
                </select>
            </div>

            <btn  :color="'grey'">
                <router-link :to="{name: 'favorites'}">
                    Назад
                </router-link>
            </btn>
            <btn @click="save($event)">Сохранить</btn>
        </form>
    </div>
</template>

<script setup>
    import btn from "../../components/btn";
    import { onMounted, computed, reactive, watch } from 'vue';
    import { storeToRefs } from "pinia";
    import {useUserStore} from "../../store/user";
    import {useAuthStore} from "../../store/auth";

    const userStore = useUserStore;
    const authStore = useAuthStore;
    const { userInfo } = storeToRefs(userStore);
    const { userAvatar } = storeToRefs(authStore);

    const editedUserInfo = reactive({
        birthday_year: 'null'
    });

    const years = computed(() => {
        let res = []
        for (let current_year = 1940; current_year <= new Date().getFullYear(); current_year++) {
            res.push(current_year)
        }
        return res
    });

    const save = (event) => {
        event.preventDefault();
        userStore.updateUser(editedUserInfo.value);
    }


    watch(userInfo, value => {
        if (Object.keys(userInfo.value).length) {
            editedUserInfo.value = {...value}
        }
    });

    onMounted(async () => {
        if (Object.keys(userInfo.value).length) {
            editedUserInfo.value = {...userInfo.value}
        }
    });

</script>

<style lang="scss" scoped>
    .select {
        border-radius: 8px;
        border: 1px solid transparent;
        outline: #000 none medium;
        overflow: visible;
        background-color: rgb(240, 242, 252);
        padding: 8px;
        &:hover {
            border-color: rgb(192, 201, 240);
            transition: border-color 0.3s ease 0s;
        }
    }
    .input {
        width: 100%;
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
        background-color: #e8e8e8;
        border: none;
        color: #333;
        border-radius: 8px;
        transition: background-color 0.3s ease 0s, border-color 0.3s ease 0s;
        padding: 0 30px;
        &:hover {
            background-color: #dcdcdc;
        }
    }
    .button:not(:last-child){
       margin-right: 15px;
    }
    a {
        text-decoration: none;
        color: #333;
    }
    .title {
        font-size: 28px;
        font-weight: 500;
    }
    .edit {
        margin-top: 20px;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.16);
        background-color: #fff;
        display: flex;
        flex-wrap: wrap;

        &__photo {
            position: relative;
            /*background-color: gray;*/
            /*border-radius: 50%;*/
            height: 200px;
            margin-right: 30px;
            width: 200px;
            & img {
                position: absolute;
                width: 100%;
                height: 100%;
            }
            & input[type="file"] {
                width: 100%;
                height: 100%;
                opacity: 0;
            }
            &-icon {
                opacity:0;
                transition: all .2s ease;
                height: 60px;
                width:60px;
                border-radius:50px;
                position: absolute;
                left:50%;
                top: 50%;
                transform: translate(-50%,-50%);
                padding: 15px;
                background-color: rgba(0, 0, 0, .5);
            }
            &:hover .edit__photo-icon {
                opacity: 1;
            }
        }
        &__form {
            &-row {
                margin-bottom: 20px;
            }
        }
    }
    @media (max-width: 500px) {
        .edit {
            &__item {
                width: 100%;
                margin-right: 0;
                &:not(:last-child) {
                    margin-bottom: 20px;
                }
            }
        }
    }

</style>
