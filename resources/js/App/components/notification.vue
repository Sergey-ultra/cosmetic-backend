<template>
    <div class="notification">
        <transition-group name="note" class="note_list">
            <div
                    v-for="(note, index) in notes"
                    :key="index"
                    class="notification__content"
                    :class="{
                        'green': note.status === 'success',
                        'red': note.status === 'error'
                    }"
            >
                <div class="notification__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" role="img" aria-hidden="true" class="v-icon__svg">
                        <path d="M12,2C17.52,2 22,6.48 22,12C22,17.52 17.52,22 12,22C6.48,22 2,17.52 2,12C2,6.48 6.48,2 12,2M11,16.5L18,9.5L16.59,8.09L11,13.67L7.91,10.59L6.5,12L11,16.5Z"></path>
                    </svg>
                </div>
                <div class="notification__text">
                    {{ note.message }}
                </div>
                <div class="notification__button" @click="notificationStore.deleteNote(index)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" role="img" aria-hidden="true" class="v-icon__svg">
                        <path d="M12,2C17.53,2 22,6.47 22,12C22,17.53 17.53,22 12,22C6.47,22 2,17.53 2,12C2,6.47 6.47,2 12,2M15.59,7L12,10.59L8.41,7L7,8.41L10.59,12L7,15.59L8.41,17L12,13.41L15.59,17L17,15.59L13.41,12L17,8.41L15.59,7Z"></path>
                    </svg>
                </div>
            </div>
        </transition-group>
    </div>
</template>

<script setup>
    import {useNotificationStore} from "../store/notification";
    import { storeToRefs } from "pinia";

    const notificationStore = useNotificationStore();
    const { notes } = storeToRefs(notificationStore);
</script>

<style lang="scss" scoped>
    .v-icon__svg {
        fill: #fff;
    }
    .green {
        background-color: green;
    }
    .red {
        background-color: red;
    }
    .notification {
        position:fixed;
        top: 50px;
        right:50px;
        z-index:1099;
        &__content {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            padding: 16px;
            border-radius:4px;
            color:#fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height:50px;
            min-width: 500px;
            margin-bottom:16px;
            /*background-color: #1e88e5;*/
        }
        &__text {
            display: flex;
            align-items: center;
        }
        &__button,
        &__icon {
            height: 24px;
            font-size: 24px;
            width: 24px;
        }
        &__icon {
            margin-right: 5px;
        }
        &__button {
            cursor:pointer;
            margin-left:auto;
        }
    }

    .note_list {
        display:flex;
        flex-direction:column;
    }
    .note-enter-from {
        transform: translateY(-80px);
        opacity:0;
    }
    .note-enter-active,.note-leave-active {
        transition: all .8s;
    }

    .note-leave-to {
        opacity:0;
        transform: translateY(100px);
    }
</style>
