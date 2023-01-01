<template>
Профиль
    <form action="">
        <div class="connect__row">
            <div class="connect__item connect__item-full">
                <span class="fio">Контактное лицо</span>
            </div>
            <div class="connect__item connect__item-full">
                <input class="input" type="text" v-model="profileForm.fio" placeholder="ФИО">
                <div class="invalid-feedback" v-for="error of v$.profileForm.fio.$errors"
                     :key="error.$uid">
                    {{ error.$message }}
                </div>
            </div>
        </div>
        <div class="connect__row">
            <div class="connect__item">
                <input class="input" type="text" v-model="profileForm.email" placeholder="Email">
                <div class="invalid-feedback" v-for="error of v$.profileForm.email.$errors"
                     :key="error.$uid">
                    {{ error.$message }}
                </div>
            </div>
            <div class="connect__item">
                <input class="input" type="text" v-model="profileForm.phone" placeholder="Телефон">
                <div class="invalid-feedback" v-for="error of v$.profileForm.phone.$errors"
                     :key="error.$uid">
                    {{ error.$message }}
                </div>
            </div>
        </div>
    </form>
</template>

<script>
import useVuelidate from "@vuelidate/core";
import {email, required} from "@vuelidate/validators";

export default {
    name: "edit",
    setup () {
        return { v$: useVuelidate() }
    },
    validations () {
        return {
            profileForm: {
                fio: {required},
                email: {required, email},
                phone:{required}
            }
        }
    },
    data() {
        return {
            profileForm: {
                fio: '',
                email: '',
                phone:''
            }
        }
    },

}
</script>

<style scoped>

</style>