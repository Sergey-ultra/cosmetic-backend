<template>
<div class="progress">
    <div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar"
         :style="{ width: getProgress }">
        {{ progress < 100 ? getProgress : "Upload Complete!" }}
    </div>
</div>
</template>

<script>
    export default {
        name: "progress-bar",
        props:{
            progress:{
                type: Number,
                default: 0
            }
        },
        computed: {
            getProgress(){
                return Math.round(this.progress) + '%'
            }
        }
    }
</script>

<style scoped>

</style>
