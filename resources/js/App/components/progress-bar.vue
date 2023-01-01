<template>
    <div class="progress">
        <div class="progress-bar progress-bar-striped progress-bar-animated"
              :style="{ width: progressLocal }"></div>
    </div>
</template>

<script setup>
    import {defineProps, computed} from "vue";

    const props = defineProps({
        progress: {
            type: Number,
            default: 0
        }
    });

    const progressLocal = computed(() => Math.round(props.progress) + '%');
</script>

<style scoped lang="scss">
    .progress {
        display: -ms-flexbox;
        display: flex;
        height: 1rem;
        overflow: hidden;
        font-size: .75rem;
        background-color: #e9ecef;
        border-radius: .25rem;
    }
    .progress-bar {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-direction: column;
        flex-direction: column;
        -ms-flex-pack: center;
        justify-content: center;
        color: #fff;
        text-align: center;
        white-space: nowrap;
        background-color: #007bff;
        transition: width .6s ease;
        &-animated {
            animation: progress-bar-stripes 1s linear infinite;
        }
        &-striped {
            background-image: linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);
            background-size: 1rem 1rem;
        }
    }
</style>
