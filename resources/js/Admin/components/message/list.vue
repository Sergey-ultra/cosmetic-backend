<template>
    <div>
        <div class="header">
            <buttonComponent :color="'grey'" @click="back">
                <fa icon="arrow-left"></fa>
            </buttonComponent>

            <div class="avatar">
                <img :src="dialogAvatar" :alt="dialogUser">
            </div>
            <div class="user-name">{{ dialogUser }}</div>
        </div>
        <div v-for="message in messages"
             :key="message.id"
             :class="{ 'message-isMine': message.is_mine }"
             class="message">

            <div :class="{ 'message__body-isMine': message.is_mine }" class="message__body">
                <div class="user-name">
                    {{ message.user_name }}
                </div>
                {{ message.message }}
                <div class="time-box">
                    <span>{{ message.created_at }}</span>
                </div>
            </div>
        </div>
        <messageForm/>
    </div>
</template>
<script setup>
import buttonComponent from '../../components/button-component.vue';
import messageForm from '../../components/message/form.vue';
import {useStore} from "vuex";
import {computed, onMounted} from "vue";

const store = useStore();


const lastMessageId = computed(() => store.state.message.lastMessageId);
const isLoadingChat = computed(() => store.state.message.isLoadingChat);
const messages = computed(() => store.state.message.messages);
const dialogUser = computed(() => store.state.message.dialogUser);
const dialogAvatar = computed(() => store.state.message.dialogAvatar);


const back = () => store.commit('message/setLastMessageId', null);


onMounted(() => {
    store.dispatch('message/loadChatByMessageId', lastMessageId.value);
})
</script>
<style scoped lang="scss">
.header {
    display: flex;
    align-items: center;
    gap:15px;
    margin-bottom: 20px;
}
.message {
    display: flex;
    gap: 8px;
    margin: 8px 50px 8px 0;
    &-isMine {
        margin: 8px 0 8px 50px;
    }
    &__body {
        flex-grow: 1;
        position: relative;
        padding: 16px;

        cursor: pointer;
        overflow: hidden;
        background-color: #f1f1f1;
        border-radius: 12px;

        &-isMine {
            background-color: #e5f4df;
        }
    }
}

.avatar {
    flex-shrink:0;
    width: 35px;
    height: 35px;
    & img {
        border-radius: 50%;
        overflow: hidden;
        height: 100%;
        width: 100%;
        object-fit: cover;
    }
}

.user-name {
    margin-bottom: 4px;
    font-weight: bold;
    font-size: 14px;
}
.time-box {
    text-align: right;
    color: #8d9399;
    font-size: smaller;
}
.open {
    margin-right: 15px;
}
</style>
