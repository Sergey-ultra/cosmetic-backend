<template>
    <div>
        <h4>Мастер-пароль</h4>
        <div>{{ masterPassword }}</div>
        <div class="box">
            <div class="box__column">
                <div
                    class="box__item"
                    v-for="(bot, index) in bots"
                    :key="index"
                >
                    <inputComponent class="input" v-model.trim="bot[0]" :color="'white'" placeholder="Введите email"/>
                    <inputComponent class="input" v-model.trim="bot[1]" :color="'white'" placeholder="Введите имя"/>
                    <inputComponent class="input" v-model.trim="bot[2]" :color="'white'" placeholder="Введите email"/>
                </div>
            </div>

            <buttonComponent class="box__column" @click="addInput">+</buttonComponent>
            <buttonComponent class="box__column" @click="removeInput" :disabled="bots.length <= 1">-</buttonComponent>
        </div>
        <div>
            <buttonComponent
                class="action"
                @click="save"
                :outline="true"
            >
               Создать
            </buttonComponent>
            {{ savingBotsStatus }}
        </div>
    </div>
</template>
<script>
import generateId from "../../utils/crypto";
import {mapActions, mapState} from "vuex";
import inputComponent from "../../components/input-component/index.vue";
import buttonComponent from "../../components/button-component.vue";



export default {
    components: {
        buttonComponent,
        inputComponent,
    },
    data() {
        return {
            bots:  [
                ['', '', generateId(16)]
            ],
        }
    },
    computed: {
        ...mapState('user', ['masterPassword', 'savingBotsStatus']),
    },
    created() {
        this.loadMasterPassword();
        console.log(this.bots);
    },
    methods: {
        ...mapActions('user', ['loadMasterPassword', 'saveUserBots']),
        addInput() {
            this.bots.push(['', '', generateId(16)])
        },
        removeInput() {
            if (this.bots.length > 1)
                this.bots.pop();
        },
        save() {
            const preparedBots = this.bots.map(el => {
                return {
                    email: el[0],
                    name: el[1],
                    password: el[2],
                }
            });
            this.saveUserBots({ bots: preparedBots });
        },
    },
}

</script>
<style scoped lang="scss">
.box {
    display: flex;
    align-items: flex-end;
    padding: 20px;
    border: 1px solid #e8ebef;
    border-radius: 8px;
    width: 70%;
    margin-bottom: 25px;
    .box__item + .box__item {
        margin-top: 10px;
    }
    &__item {

        .input + .input {
            margin-left: 15px;
        }
    }
    &__column + .box__column {
        margin-left: 15px;
    }
}
.input {
    width: auto;
}
</style>
