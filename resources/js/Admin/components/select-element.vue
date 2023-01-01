<template>
    <div class="select" >
        <slot name="activator" :on="showInner" ></slot>

        <div class="select__inner" v-if="isShowInner" ref="inner">
            <slot></slot>
        </div>
    </div>
</template>

<script>
    export default {
        name: "select",
        data: () => ({
            isShowInner: false,
        }),
        methods: {
            outsideClick(event) {
                if (!this.$refs.inner.contains(event.target)) {
                    event.stopPropagation();
                    this.isShowInner = false;
                    document.removeEventListener('click', this.outsideClick, { capture: true })
                }
            },
            showInner(event) {
                this.isShowInner = true
                document.addEventListener('click', this.outsideClick, {capture: true})
            }
        }
    }
</script>

<style scoped lang="scss">
.select {
    position: relative;
    &__inner {
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
        z-index: 1099;
        min-width: 10rem;
        width:100%;
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
    }
}
</style>
