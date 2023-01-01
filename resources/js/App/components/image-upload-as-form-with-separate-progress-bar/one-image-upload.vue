<template>
    <div class="file-form">
        <img :src="previewImage" class="">

        <progress-bar
                v-if="isUploading"
                :progress="progress"
                class="indicator"
        />

        <div class="file-form__img-layer">
            <div class="file-form__close" @click="deletePhoto(index)">×</div>
            <div v-if="index !== mainPhotoIndex"  class="main-photo-title"><span>Сделать главной</span></div>
            <div v-else class="main-photo-title">Главное фото</div>
        </div>
    </div>
</template>

<script setup>
    import ProgressBar from '../progress-bar'
    import {defineProps, defineEmits, ref, onMounted} from "vue";
    import api from "../../utils/api";

    const emit = defineEmits(['setEditedImageUrlsByIndex', 'deleteFromEditedImageUrlsByIndex']);
    const props = defineProps({
        file: {
            default: null
        },
        index: Number,
        folder: String,
        mainPhotoIndex: {
            type: Number,
            default: 0
        }
    });


    const isUploading = ref(false);
    const progress = ref(0);
    const previewImage = ref('');
    const uploadingImageUrl = ref('');

    const initFile = () => {
        if (props.file) {
            // console.log(this.file instanceof File)
            if (props.file instanceof File) {
                let reader = new FileReader();
                reader.readAsDataURL(props.file);
                reader.onload = e => previewImage.value = e.target.result;

                loadSelectedToBackend(props.file);
            } else {
                previewImage.value = props.file;
            }
        }
    };

    const loadSelectedToBackend = async file => {
        isUploading.value = true;

        let form = new FormData();
        form.append('folder', props.folder)
        form.append('images[]', file)


        const {data} = await api.post('/images', form, {
            headers: {'Content-Type': 'multipart/form-data'},
            onUploadProgress: e => {
                if (e.lengthComputable) {
                    progress.value = (e.loaded / e.total) * 100
                }
            }
        })
        if (data && Array.isArray(data)) {
            emit('setEditedImageUrlsByIndex', {
                index: this.index,
                url: data[0]
            })
        }
        isUploading.value = false;
    };

    const deletePhoto = () => {
        previewImage.value = '';
        emit('deleteFromEditedImageUrlsByIndex', props.index);
    };

    onMounted(() => initFile());
</script>

<style scoped lang="scss">
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