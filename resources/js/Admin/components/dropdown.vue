<template>
    <div class="dropdown" >
        <slot name="activator" :on="showDropdown" ></slot>

        <div class="dropdown__inner" v-if="isShowDropdown" ref="dropdownBox">
            <slot></slot>
        </div>
    </div>
</template>

<script>
    export default {
        name: "dropdown",
        data: () => ({
            isShowDropdown: false,
        }),
        methods: {
            outsideClick(event) {
                if (!this.$refs.dropdownBox.contains(event.target)){
                    event.stopPropagation()
                }
                this.isShowDropdown = false

            },
            showDropdown(event) {
                this.isShowDropdown = true
                document.addEventListener('click', this.outsideClick, {capture: true, once: true})
            }
        }
    }
</script>

<style scoped lang="scss">
    .dropdown {
        position: relative;
        &__inner {
            position: absolute;
            bottom: 0;
            right: 0;
            transform: translateY(calc(100% + 11px));
            z-index: 1099;
            min-width: 10rem;
            font-size: 1rem;
            color: #212529;
            text-align: left;
            list-style: none;
            padding: 8px 8px 12px;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #999999;
            border-radius: .25rem;
            box-shadow:rgba(0, 16, 61, 0.32) 0px 4px 32px 0px;
            &:before {
                content: ' ';
                position: absolute;
                width: 0;
                height: 0;
                left: 85%;
                bottom: 100%;
                transform: translateX(-50%);
                border: 11px solid transparent; /* Прозрачные границы */
                border-bottom: 11px solid #999999;
            }
            &:after {
                content: ' ';
                position: absolute;
                width: 0;
                height: 0;
                left: 85%;
                bottom: 100%;
                transform: translateX(-50%);
                border: 10px solid transparent; /* Прозрачные границы */
                border-bottom: 10px solid #fff;
            }
        }
    }
</style>
