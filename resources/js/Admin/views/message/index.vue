<template>
    <div class="wrapper">
        <div v-if="lastMessageId">
            <messageList/>
        </div>
        <div v-else>
            <div
                class="chat"
                v-for="message in chats"
                :key="message.id"
                @mouseover="setOpenChat(message.id)"
                @mouseout="setOpenChat(null)"
                @click="openChat(message.id)"
            >
                <div class="avatar">
                    <img :src="message.avatar" alt="">
                </div>
                <div class="body">
                    <div class="user-name">
                        {{ message.user_name }}
                    </div>
                    <div>
                        {{ message.message }}
                        <div class="time-box">
                            <span class="open" v-if="openChatId === message.id">Открыть беседу</span>
                            <span>{{ message.created_at }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import messageList from '../../components/message/list.vue';
import { useStore } from 'vuex';
import {ref, onMounted, computed} from 'vue';

const store = useStore();

const chats = computed(() => store.state.message.adminMessages);
const lastMessageId = computed(() => store.state.message.lastMessageId);
const openChatId = ref(null);


const setOpenChat = id => openChatId.value = id;

const openChat = id => store.commit('message/setLastMessageId', id);




onMounted(async () => {
    await store.dispatch('message/loadPriceOptions');
});
</script>
<style scoped lang="scss">

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
.chat {
    position: relative;
    padding: 16px;
    cursor: pointer;
    overflow: hidden;
    border-bottom: 1px solid #e2e6e9;
    &:hover {
        background-color: #f5f7f9;
    }
}
.avatar {
    flex-shrink:0;
    width: 50px;
    height: 50px;
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
