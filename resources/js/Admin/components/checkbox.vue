<template>
    <label class="checkbox">
        <input
            class="checkbox__input"
            type="checkbox"
            v-model="localIsChecked"
        >
        <span class="checkbox__checkmark">

        </span>
    </label>
</template>

<script>
    export default {
        name: "checkbox",
        props: {
            isChecked: {
                type:Boolean,
            }
        },
        computed:{
            localIsChecked:{
                get(){
                    return this.isChecked
                },
                set(value) {
                    this.$emit('update:isChecked', value)
                }
            }
        }
    }
</script>

<style lang="scss" scoped>
    .checkbox {
        display: inline-block;
        position: relative;
        margin: 6px 0;
        padding-left: 5px;
        font-size: 14px;
        line-height: 24px;
        &__checkmark {
            content: "";
            display: block;
            width: 14px;
            height: 14px;
            border: 1px solid #ccc;
            border-radius: 2px;
            background-color: #fff;
            position: absolute;
            top: -6px;
            left: 0;
        }
        &__input {
            display: none;
            &:checked + .checkbox__checkmark {
                background-color: rgb(24, 103, 192);
                border: none;
                &::after {
                    content: "";
                    display: block;
                    width: 7px;
                    height: 4px;
                    border-bottom: 2px solid #fff;
                    border-left: 2px solid #fff;
                    position: absolute;
                    top: 45%;
                    left: 51%;
                    transform: translate(-50%,-50%) rotate(-45deg);
                }
            }
        }
    }

</style>
