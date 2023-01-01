<template>
    <article class="client-content">
        <h3 class="client-content__title">
            Фотографии и видео покупателей
            <span v-if="total && reviewImages.length">({{ reviewImages.length }})</span>
        </h3>

        <div v-if="!(total && reviewImages.length)">
            <div>Их пока нет — станьте первым, кто покажет товар в жизни</div>
            <button class="client-content__btn" @click="showImageLoadingForm">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                     data-tid="185d8c23 a4b74b6c">
                    <path d="M21.5986 5.00005L18.2363 5.00295L16.6 1.99805H9.3857L7.8008 5.00295H4.3994C4.02825 5.00348 3.67246 5.15122 3.41011 5.41376C3.14775 5.6763 3.00026 6.03219 3 6.40335V12.998H5V7.00295H9.0078L10.5928 3.99805H15.4121L17.0488 7.00295L21 7.00005L20.999 17.999H9.999V19.999H21.599C21.9701 19.9987 22.3259 19.8511 22.5883 19.5888C22.8508 19.3265 22.9985 18.9707 22.999 18.5996L23 6.40005C22.9995 6.02865 22.8517 5.67262 22.5889 5.41013C22.3262 5.14764 21.97 5.00015 21.5986 5.00005Z"
                          fill="#222222"></path>
                    <path d="M12.998 16.5C13.888 16.5 14.758 16.2361 15.4981 15.7416C16.2381 15.2471 16.8148 14.5443 17.1554 13.7221C17.496 12.8998 17.5852 11.995 17.4115 11.1221C17.2379 10.2492 16.8093 9.44736 16.18 8.81802C15.5506 8.18869 14.7488 7.7601 13.8759 7.58647C13.003 7.41283 12.0982 7.50195 11.2759 7.84254C10.4536 8.18314 9.75084 8.75991 9.25637 9.49994C8.76191 10.24 8.49799 11.11 8.49799 12C8.49928 13.1931 8.97381 14.3369 9.81744 15.1805C10.6611 16.0242 11.8049 16.4987 12.998 16.5ZM12.998 9.5C13.4924 9.5 13.9758 9.64662 14.3869 9.92133C14.798 10.196 15.1185 10.5865 15.3077 11.0433C15.4969 11.5001 15.5464 12.0028 15.4499 12.4877C15.3535 12.9727 15.1154 13.4181 14.7658 13.7678C14.4161 14.1174 13.9707 14.3555 13.4857 14.452C13.0008 14.5484 12.4981 14.4989 12.0413 14.3097C11.5845 14.1205 11.194 13.8001 10.9193 13.3889C10.6446 12.9778 10.498 12.4945 10.498 12C10.4986 11.3371 10.7622 10.7016 11.2309 10.2329C11.6996 9.7642 12.3351 9.50061 12.998 9.5Z"
                          fill="#222222"></path>
                    <path d="M3 22.999H5V19.999H8V17.999H5V14.999H3V17.999H0V19.999H3V22.999Z"
                          fill="#222222"></path>
                </svg>
                <div class="client-content__text">Добавить</div>
            </button>
        </div>
        <div v-else class="client-content__images" >
            <div class="client-content__inner" @click="showImageLoadingForm">
                <button class="client-content__image__btn client-content__image__btn-icon" type="button">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <path d="M21.5986 5.00005L18.2363 5.00295L16.6 1.99805H9.3857L7.8008 5.00295H4.3994C4.02825 5.00348 3.67246 5.15122 3.41011 5.41376C3.14775 5.6763 3.00026 6.03219 3 6.40335V12.998H5V7.00295H9.0078L10.5928 3.99805H15.4121L17.0488 7.00295L21 7.00005L20.999 17.999H9.999V19.999H21.599C21.9701 19.9987 22.3259 19.8511 22.5883 19.5888C22.8508 19.3265 22.9985 18.9707 22.999 18.5996L23 6.40005C22.9995 6.02865 22.8517 5.67262 22.5889 5.41013C22.3262 5.14764 21.97 5.00015 21.5986 5.00005Z"
                              fill="#222222"></path>
                        <path d="M12.998 16.5C13.888 16.5 14.758 16.2361 15.4981 15.7416C16.2381 15.2471 16.8148 14.5443 17.1554 13.7221C17.496 12.8998 17.5852 11.995 17.4115 11.1221C17.2379 10.2492 16.8093 9.44736 16.18 8.81802C15.5506 8.18869 14.7488 7.7601 13.8759 7.58647C13.003 7.41283 12.0982 7.50195 11.2759 7.84254C10.4536 8.18314 9.75084 8.75991 9.25637 9.49994C8.76191 10.24 8.49799 11.11 8.49799 12C8.49928 13.1931 8.97381 14.3369 9.81744 15.1805C10.6611 16.0242 11.8049 16.4987 12.998 16.5ZM12.998 9.5C13.4924 9.5 13.9758 9.64662 14.3869 9.92133C14.798 10.196 15.1185 10.5865 15.3077 11.0433C15.4969 11.5001 15.5464 12.0028 15.4499 12.4877C15.3535 12.9727 15.1154 13.4181 14.7658 13.7678C14.4161 14.1174 13.9707 14.3555 13.4857 14.452C13.0008 14.5484 12.4981 14.4989 12.0413 14.3097C11.5845 14.1205 11.194 13.8001 10.9193 13.3889C10.6446 12.9778 10.498 12.4945 10.498 12C10.4986 11.3371 10.7622 10.7016 11.2309 10.2329C11.6996 9.7642 12.3351 9.50061 12.998 9.5Z"
                              fill="#222222"></path>
                        <path d="M3 22.999H5V19.999H8V17.999H5V14.999H3V17.999H0V19.999H3V22.999Z"
                              fill="#222222"></path>
                    </svg>
                </button>
            </div>
            <div
                    class="client-content__inner"
                    v-for="image in reviewImages"
                    :key="image"
            >
                <button class="client-content__image__btn" type="button">
                    <div class="client-content__image">
                        <img :src="image">
                    </div>
                </button>
            </div>
        </div>

        <add-photos-modal
                v-if="isShowImageLoadingForm"
                v-model:isShowImageLoadingForm="isShowImageLoadingForm"
        />
    </article>
</template>

<script setup>
    import addPhotosModal from "../review-list/add-photos-modal";
    import {useReviewStore} from "../../store/review";
    import {useCurrentSkuStore} from "../../store/currentSku";
    import {onMounted, ref, watch} from "vue";
    import {storeToRefs} from "pinia";


    const isShowImageLoadingForm = ref(false);
    const reviewStore = useReviewStore();
    const currentSkuStore = useCurrentSkuStore();

    const { total, reviewImages, isLoadingReviewImages } = storeToRefs(reviewStore);
    const { currentSkuId } = storeToRefs(currentSkuStore);

    const showImageLoadingForm = () => isShowImageLoadingForm.value = true;

    watch(currentSkuId,
        () => {
            reviewStore.loadReviewAdditionalInfo();
        }
    );

    onMounted(() => {
        reviewStore.loadReviewAdditionalInfo()
    });
</script>

<style scoped lang="scss">
    .client-content {
        border-bottom: 1px solid #e8e8e8;
        padding: 20px 0;
        &__title {
            font-size: 16px;
            line-height: 16px;
            font-weight: 700;
            margin-bottom: 16px;
        }
        &__btn {
            outline: none;
            outline-offset: 2px;
            margin: 0;
            padding: 0;
            border: none;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 36px;
            width: 121px;
            background: #e8e8e8;
            border-radius: 4px;
            margin-top: 16px;
        }
        &__text {
            margin-left: 9px;
        }
        &__inner {
            padding-left: 6px;
            padding-right: 6px;
            display: inline-block;
            box-sizing: border-box;
            vertical-align: top;
        }
        &__image__btn {
            width: auto;
            margin: 0;
            padding: 0;
            position: relative;
            border: 2px solid #fff;
            border-radius: 6px;
            cursor: pointer;
            outline: none;
            outline-offset: 2px;
            &-icon {
                background-color: #fff;
                border-radius: 8px;
                border: 1px solid #ccc;
                display: flex;
                justify-content: center;
                align-items: center;
                width: 68px;
                height: 71px;
                &:hover {
                    border-width: 2px;
                }
            }
        }
        &__image {
            width: 68px;
            padding-top: 68px;
            position: relative;
            & img {
                border-radius: 4px;
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-position: 50%;
                background-repeat: no-repeat;
            }
        }
    }
</style>