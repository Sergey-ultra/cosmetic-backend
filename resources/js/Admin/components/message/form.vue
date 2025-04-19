<template>
    <form @submit.prevent="sendMessage">
        <div v-if="error" class="alert-warning">
            {{ error }}
        </div>
        <div class="form__group">
            <label>
                <div class="label">
                    <span class="text-gray">
                        Добавить сообщение
                    </span>
                </div>
                <textarea-component v-model="message" rows="6" :color="'white'"/>
            </label>
            <div v-for="error of v$.message.$errors" :key="error.$uid" class="invalid-feedback">
                {{ error.$message }}
            </div>
        </div>
        <buttonComponent type="submit">
            Отправить
        </buttonComponent>
    </form>
</template>
<script setup>
import textareaComponent from '../../components/textarea-component.vue';
import buttonComponent from '../../components/button-component.vue';
import {helpers, required} from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import {computed, ref} from "vue";
import {useStore} from "vuex";

const store = useStore();

const message = ref('');
const error = ref('');
const dialogUserId = computed(() => store.state.message.dialogUserId)

const rules = {
    message: {
        required: helpers.withMessage('Поле должно быть заполнено', required),
    },
}

const v$ = useVuelidate(rules, { message });

const sendMessage = async() => {
    const validated = await v$.value.$validate();
    error.value = '';

    if (validated) {
        const responseMessage = await store.dispatch('message/sendMessage', {
            message: message.value,
            dialog_user_id: dialogUserId.value,
        });

        if (responseMessage) {
            error.value = responseMessage;
        }
        message.value = '';
        v$.value.$reset();
    }
};
</script>
<style lang="scss">
.form__group {
    margin: 12px 0;
    width: 100%;
}
</style>
