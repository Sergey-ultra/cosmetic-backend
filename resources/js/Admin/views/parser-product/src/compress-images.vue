<template>
    <buttonComponent
        :size="'small'"
        @click="compressAllUncompressedImages"
    >
        Сжать несжатые картинки
    </buttonComponent>
</template>

<script>
    import {mapActions} from "vuex";
    import buttonComponent from "../../../components/button-component.vue";

    export default {
        components: {
            buttonComponent
        },
      methods: {
          ...mapActions('productParser', ['compressAllUncompressedImages']),
      }
    }
</script>

