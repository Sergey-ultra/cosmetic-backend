<template>
    <button class="button"  :style="styleObject" type="button">
        <slot></slot>
    </button>
</template>

<script>
    export default {
        name: "m-button",
        props: {
            height: {
                type: Number,
                default: 28
            },
            color: {
                type: String,
                // default: '#1867c0',
                default: 'rgb(0, 91, 255)'
            },
            disabled: {
                type: Boolean,
                default: false
            }
        },
        watch:{
            disabled(value){
                console.log(value)
            }
        },
        computed:{
            styleObject(){
                let styles = {
                    height: this.height + 'px',
                    background: this.color,
                    border: `1px solid ${this.color}`
                }
                if (this.disabled) {
                    styles.background = '#d5dae0'
                    styles.border = 'none'
                }
                return styles
            }
        }
    }
</script>

<style lang="scss" scoped>
    .button {
        min-width: 28px;
        padding: 0 12.4px;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 4px;
        color:#fff;
        &[disabled] {
            background: #d5dae0;
            border:none;
        }
    }

    .button a {
        color:#fff;
        text-decoration: none;
    }

    .button:hover {
        background: #45c661;
    }
</style>
