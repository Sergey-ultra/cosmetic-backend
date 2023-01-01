<template>
    <button class="button"  :style="styleObject" type="button">
        <slot></slot>
    </button>
</template>

<script setup>
    import {defineProps, computed} from "vue";

    const props = defineProps({
        height: {
            type: Number,
            default: 28
        },
        color: {
            type: String,
            default: 'blue'
        },
        disabled: Boolean
    });


    const background = computed(() => {
        switch (props.color) {
            case 'grey':
                return '#eee';
            case 'red':
                return '#FE8695';
            case 'coffee':
                return '#434343';
            default:
                return '#1867c0';
        }
    });
    // fontColor () {
    //     if (this.color === 'grey') {
    //         return '#333'
    //     }
    //     return  '#fff'
    // },
    const styleObject = computed(() => {
        let styles = {
            height: this.height + 'px',
            background: background.value,
            border: `1px solid ${background.value}`,
        };
        if (props.disabled) {
            styles.background = '#d5dae0';
            styles.border = 'none';
        }
        return styles;
    });

</script>

<style lang="scss" scoped>
    .button {
        min-width: 28px;
        padding: 0 12.4px;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        border-radius: 4px;
        color:#fff;
        position: relative;
        transition: background-color 0.3s ease 0s, border-color 0.3s ease 0s;
        & a {
            color:#fff;
            text-decoration: none;
        }
        &:hover {
            background: #45c661;
        }
        &:before {
            background-color: currentColor;
            border-radius: inherit;
            bottom: 0;
            color: inherit;
            content: "";
            left: 0;
            opacity: 0;
            pointer-events: none;
            position: absolute;
            right: 0;
            top: 0;
            transition: opacity .2s cubic-bezier(.4,0,.6,1);
        }
        &:hover::before {
            opacity: .18;
        }
    }
</style>
