<template>
    <div class="file-form__img">
        <img v-if="localImage" :src="localImage" :alt="localImage" class="">

        <div class="file-form__layer">
            <div class="file-form__description"  v-if="!localImage">
                <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24"
                     stroke-linecap="round" stroke-linejoin="round" height="1em" width="1em"
                     xmlns="http://www.w3.org/2000/svg">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                    <polyline points="21 15 16 10 5 21"></polyline>
                </svg>
                <p>Добавьте одну фотографию</p>
            </div>
            <tool-tip
                    :position="'left'"
                    class="file-form__close"
                    v-else
            >
                <template v-slot:content>
                    <span>Скрыть баннер</span>
                </template>
                <div  @click="deleteImage">✕</div>
            </tool-tip>

            <input @change="setImage($event)" type="file"  accept="image/*">
        </div>


    </div>
</template>

<script>
    import toolTip from './tool-tip.vue'
    export default {
        name: "one-image-upload",
        components: {
            toolTip
        },
        data() {
            return {
                previewImage: ''
            }
        },
        props: {
            image: String,
            height: {
                type:Number,
                default: 113
            }
        },
        computed: {
            localImage: {
                get() {
                    return this.image
                },
                set(value) {
                    this.$emit('update:image', value)
                }
            }
        },
        methods: {
            setImage (event) {
                const file  = event.target.files[0] || event.dataTransfer.files[0]
                const reader = new FileReader()
                reader.readAsDataURL(file)
                reader.onload = e => this.localImage = e.target.result
            },
            deleteImage() {
                this.localImage = null
            },
        }
    }
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
        &__description{
            position: absolute;
            display:flex;
            align-items:center;
            flex-direction: column;
            top: 20%;
            left: 50%;
            width: 100%;
            font-size: 2rem;
            color: #598cff;
            transform: translate(-50%);
            & p {
                font-size: .8rem;
                color: #ababab;
            }
        }
        &__img {
            width: calc(20% - 16px);
            height: 113px;
            margin: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 1px solid #e6e6e6;
            position: relative;
            border-radius: 4px;
            text-align: center;
            transition: border-color .3s;
            &  img {
                max-height: 100%;
                max-width: 100%;
            }
            & input[type="file"] {
                width: 100%;
                height: 100%;
                opacity: 0;
                cursor:pointer;
            }
            &:hover .file-form__layer-has {
                background: #000;
                opacity: 0.6;
            }
        }
        &__layer{
            z-index: 1;
            position: absolute;
            top: 0;
            bottom: 0;
            right: 0;
            left: 0;
            cursor: pointer;
        }
        &__close {
            position: absolute;
            top: -35px;
            right: -35px;
            overflow: hidden;
            transition: color .1s ease-out;
            border: none;
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
            height: 20px;
            width: 20px;
            border-radius: 50%;
            text-align: center;
            text-decoration: none;
            line-height: 24px;
            z-index: 10;
            background-color: rgba(0,0,0,0.3);
            &:hover {
                background: #000;
                transform: scale(1.1);
                transition: all 0.2s ease;
            }

        }
    }
</style>
