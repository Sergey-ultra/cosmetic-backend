<template>
    <div class="filter">
        <div class="filter__show">Показать отзывы с оценкой</div>
        <router-link
            :to="{
                name: 'reviews',
                params: { ...$route.params, savePosition: mode ==='review'},
                query: { rating: getLinkQuery(rating) }
            }"
            class="filter__chunk"
            v-for="(reviewsCount, rating) in ratingFilter"
            :key="rating"

        >
            <input
                v-if="mode ==='review'"
                type="checkbox"
                :value="rating"
                v-model="currentRatingFilter"
            >
            <ratingView
                :rating="Number(rating)"
                :isShowText="false"
            />
            <span class="filter__chunk__count">
                {{ reviewsCount }} отзыв
            </span>
        </router-link>
    </div>
</template>

<script setup>
    import ratingView from "../ratingView";
    import {useReviewStore} from "../../store/review";
    import {useCurrentSkuStore} from "../../store/currentSku";
    import {onBeforeMount, ref, defineProps, watch} from "vue";
    import { storeToRefs } from "pinia";
    import {useRoute} from "vue-router";

    const reviewStore = useReviewStore();
    const currentSkuStore = useCurrentSkuStore();
    const { ratingFilter } = storeToRefs(reviewStore);
    const { currentSku } = storeToRefs(currentSkuStore);

    const currentRatingFilter = ref([]);

    const route = useRoute();
    const props = defineProps({
        mode: {
            type: String,
            default: 'default'
        }
    });

    const setCurrentRatingFilter = query => {
        if (query.hasOwnProperty('rating')) {
            let ratings = query.rating
            if (!Array.isArray(ratings)) {
                ratings = [ratings]
            }
            currentRatingFilter.value = ratings.map(rating => Number(rating))
        }
    };

    const getLinkQuery = rating => {
        let array = [...currentRatingFilter.value];
        const index = array.indexOf(Number(rating));
        if (index === -1) {
            array.push(Number(rating))
        } else {
            array.splice(index, 1)
        }

        return array
    };


    watch(
        route.query,
        value => setCurrentRatingFilter(value),
        {deep: true}
    );

    onBeforeMount(() => setCurrentRatingFilter(route.query));

    // toggleCurrentRatingFilter(event, rating) {
    // //@click.native.capture="mode ==='review' ? toggleCurrentRatingFilter($event, rating) : ''"
    //     event.preventDefault()
    //     console.log(event.target)
    //     const index = this.currentRatingFilter.indexOf(Number(rating));
    //     if (index !== -1) {
    //         this.currentRatingFilter.push(Number(rating))
    //     } else {
    //         this.currentRatingFilter.splice(index, 1)
    //     }
    //     this.setRatingQuery()
    // },
    // setRatingQuery() {
    //     const query = {...this.$route.query}
    //     query.rating = [...this.currentRatingFilter]
    //     this.$router.push({query, params: {savePosition: true}})
    // },

</script>

<style scoped lang="scss">
    .filter {
        &__chunk {
            color: #04b;
            text-decoration: none;
            display: flex;
            align-items: center;
            user-select: none;
            cursor: pointer;
            padding: 8px 0;
            & > :not(:first-child) {
                padding-left: 12px;
            }
        }
        &__show {
            font-family: YS Text,sans-serif;
            font-style: normal;
            font-stretch: normal;
            font-size: 18px;
            line-height: 24px;
            font-weight: 400;
            margin-bottom: 16px;
        }
    }
</style>