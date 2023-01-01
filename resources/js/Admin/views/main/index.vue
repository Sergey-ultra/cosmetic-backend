<template>
    <div class="main">
        <div class="main__row">
            <div class="main__item">
                <h2>Количество отслеживаемых</h2>
                <tracking-chart :chartData="trackingDynamics" class="main__chart"/>
            </div>
            <div class="main__item">
                <h2>Количество посещений</h2>
                <visit-chart :chartData="visitStatistics" class="main__chart"/>
            </div>
        </div>

        <div class="main__row">
            <div class="main__item">
                <h2>Клики по дате</h2>
                <div class="main__element" v-for="click in linkClicksByDateStatistics" :key="click.date">
                    <span class="main__key">{{ click.date }}</span>
                    <span class="main__value">{{ click.count }}</span>
                </div>
            </div>

            <div class="main__item">
                <h2>Количество кликов</h2>
                <div v-for="store in linkClicksByStoresStatistics" :key="store.name">
                    <span>{{ store.name }}</span>
                    <span>{{ store.sum }}</span>
                </div>
            </div>
        </div>

        <div class="main__row">
            <div class="main__item">
                <h2>Количество рейтингов</h2>
                <div v-for="rating in ratingStatistics" :key="rating.date">
                    <span>{{ rating.date }}</span>
                    <span>{{ rating.count }}</span>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    import trackingChart from '../../components/main/trackingChart.vue'
    import visitChart from '../../components/main/visitChart.vue'
    import reviewsCountChart from '../../components/main/reviewsCountChart.vue'
    import {mapActions, mapState} from "vuex";
    export default {
        name: "main",
        components: {
            trackingChart,
            visitChart,
            reviewsCountChart
        },
        data() {
            return {

            }
        },
        computed: {
            ...mapState('dynamics', ['trackingDynamics', 'visitStatistics', 'linkClicksByStoresStatistics',
                'linkClicksByDateStatistics','ratingStatistics']),
            ...mapState('review', ['reviewsDynamics']),
        },
        created() {
            this.loadTrackingDynamics()
            this.loadVisitStatistics()
            this.loadReviewsDynamics()
            this.loadLinkClicksByStoresStatistics()
            this.loadLinkClicksByDateStatistics()
            this.loadRatingStatistics()
        },
        methods: {
            ...mapActions('dynamics', ['loadTrackingDynamics', 'loadVisitStatistics', 'loadLinkClicksByStoresStatistics',
                'loadLinkClicksByDateStatistics', 'loadRatingStatistics']),
            ...mapActions('review', ['loadReviewsDynamics']),
        }
    }
</script>

<style lang="scss" scoped>
    .main {
        display: flex;
        flex-direction: column;
        &__row {
            width: 100%;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        &__item {
            height: 300px;
            width: 50%;
            margin-bottom: 65px;
        }
        &__element {
            display:flex;
        }
        &__key {
            width: 30%;
        }
        &__chart {
            height: 300px;
        }
    }
    @media (max-width: 780px) {
        .main {
            &__item {
                width: 100%;
            }
        }
    }
</style>
