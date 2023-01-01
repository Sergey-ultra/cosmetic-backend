<template>
    <div class="file-form">
        <one-image-upload
                v-for="(file, index) in files"
                :key="index"
                :file="file"
                :folder="folder"
                :index="index"
                :mainPhotoIndex="mainPhotoIndex"
                @setEditedImageUrlsByIndex="setEditedImageUrlsByIndex"
                @deleteFromEditedImageUrlsByIndex="deleteFromEditedImageUrlsByIndex"
                class="file-form__item file-form__img"
                :style="{width: width, height: `${height}px`}"
        />

        <div  class="file-form__item file-form__upload"     :style="{width: width, height: `${height}px`}">
            <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24"
                 stroke-linecap="round" stroke-linejoin="round" height="1em" width="1em"
                 xmlns="http://www.w3.org/2000/svg">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                <polyline points="21 15 16 10 5 21"></polyline>
            </svg>
            <p>Добавьте свои фотографии (до 10
                штук)<span>*</span></p>
            <input id="formFileMultiple" @change="loadImages($event)" type="file" name="images"  multiple accept="image/*">
        </div>
    </div>
</template>

<script setup>
    import oneImageUpload from './one-image-upload'
    import {reactive, defineProps, defineEmits,  ref, computed, watch} from "vue";

    const emit = defineEmits(['update:initialImageUrls']);

    const props = defineProps({
        folder: String,
        initialImageUrls: Array,
        photoCountInRow: {
            type: Number,
            default: 5
        },
        height: {
            type: Number,
            default: 113
        }
    });

    const mainPhotoIndex = ref(0);
    const files = reactive([]);


    const width = computed(() => `calc(100% /  ${props.photoCountInRow}  - 16px)`);

    const loadImages = event => {
        const loaded = event.target.files || event.dataTransfer.files;
        for (const file of loaded) {
            files.value.push(file);
        }
    };
    const setEditedImageUrlsByIndex = ({index, url}) => {
        let array = [...props.initialImageUrls];
        array[index] = url;
        emit('update:initialImageUrls', array);
    };
    const deleteFromEditedImageUrlsByIndex = index => {
        let array = [...props.initialImageUrls];
        array.splice(index, 1);
        emit('update:initialImageUrls', array);
    };

    watch(props.initialImageUrls, value => {
        if (value.length) {
            files.value = [...value];
        }
    });
</script>

<style lang="scss" scoped>
    .file-form {
        display: flex;
        justify-content: flex-start;
        flex-wrap: wrap;
        align-items: flex-start;
        border: 1px dashed #d9d9d9;
        border-radius: 4px;
        overflow: hidden;
        &__item {
            margin: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 1px solid #e6e6e6;
            position: relative;
            border-radius: 4px;

            &  img {
                max-height: 100%;
                max-width: 100%;
            }
        }
        &__img {
            &-layer{
                z-index: 1;
                display:none;
                position: absolute;
                top: 0;
                bottom: 0;
                right: 0;
                left: 0;
                background: #000;
                opacity: .6;
            }
            &:hover .file-form__img-layer{
                display:block;
            }
        }
        &__close {
            position: absolute;
            top: 0;
            right: 0;
            overflow: hidden;
            transition: color .1s ease-out;
            border: none;
            border-radius: 0;
            outline: initial;
            background: 0 0;
            font-family: serif;
            font-size: 30px;
            cursor: pointer;
            color:#fff;
            display: flex;
            justify-content: center;
            align-items:center;
            margin: 10px 10px 0 0;
            height: 15px;
            width: 15px;
        }
        &__upload {
            text-align: center;
            transition: border-color .3s;

            & svg {
                position: absolute;
                top: 10%;
                left: 50%;
                font-size: 2rem;
                color: #598cff;
                transform: translate(-50%);
            }
            & p {
                font-size: .8rem;
                color: #ababab;
                position: absolute;
                top: 39%;
                width:80%;
                left: 50%;
                transform: translate(-50%);
            }
            & input[type="file"] {
                width: 100%;
                height: 100%;
                opacity: 0;
                cursor: pointer;
            }
        }
    }
    .indicator {
        z-index: 1;
        position: absolute;
        right: 0;
        left: 0;
        bottom: 0;
    }
    .main-photo-title {
        z-index: 2;
        line-height: 30px;
        position: absolute;
        background: rgba(0,0,0,.7);
        color:#fff;
        padding-left: 10px;
        height: 30px;
        right: 0;
        left: 0;
        bottom: 0;
    }
</style>
