<template>
    <div class="dropdown" >
        <slot name="activator" :on="showDropdown" ></slot>

        <div class="drop__inner" v-if="isShowDropdown" ref="dropdownBox">
            <slot></slot>
        </div>
    </div>
</template>

<script setup>
    import {ref} from "vue";

    const isShowDropdown = ref(false);
    const dropdownBox = ref(null);

    const outsideClick = event => {
        if (!dropdownBox.value.contains(event.target)) {
            event.stopPropagation();
        }
        isShowDropdown.value = false;
    };

    const showDropdown = event => {
        isShowDropdown.value = true;
        document.addEventListener('click',outsideClick, {capture: true, once: true});
    };

</script>

<style scoped lang="scss">
    .dropdown {
        position: relative;
    }

    .drop__inner {
        position: absolute;
        top: calc(100% + 11px);
        right: 0;
        z-index: 1099;
        min-width: 320px;
        font-size: 1rem;
        color: #212529;
        text-align: left;
        list-style: none;
        padding: 8px 8px 12px;
        background-color: #fff;
        background-clip: padding-box;
        border-radius: 8px;
        box-shadow: 0 5px 46px rgba(0,0,0,.12),0 7px 15px rgba(0,0,0,.12);
    }
    .drop__inner:before {
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
    .drop__inner:after {
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



</style>
