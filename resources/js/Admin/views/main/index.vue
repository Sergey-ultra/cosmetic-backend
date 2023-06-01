<template>
    <tabs :tabList="tabList">
        <template v-slot:tabPanel-1>
            <div class="main">
                <div class="main__row">
                    <div class="main__item">
                        <div class="main__title">Клики по дате</div>
                        <div class="main__wrapper">
                            <div class="main__element" v-for="click in linkClicksByDateStatistics" :key="click.date">
                                <span class="main__key">{{ click.date }}</span>
                                <span class="main__value">{{ click.count }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="main__item">
                        <div class="main__title">Количество кликов</div>
                        <div class="main__wrapper">
                            <div v-for="store in linkClicksByStoresStatistics" :key="store.name">
                                <span>{{ store.name }}</span>
                                <span>{{ store.sum }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="main__row">
                    <div class="main__item">
                        <div class="main__title">Количество рейтингов</div>
                        <div class="main__wrapper">
                            <div v-for="rating in ratingStatistics" :key="rating.date">
                                <span>{{ rating.date }}</span>
                                <span>{{ rating.count }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template v-slot:tabPanel-2>
            <tracking-chart/>
        </template>
        <template v-slot:tabPanel-3>
            <visit-chart/>
        </template>
    </tabs>
</template>

<script>
    import trackingChart from '../../components/main/trackingChart.vue'
    import visitChart from '../../components/main/visitChart.vue'
    import reviewsCountChart from '../../components/main/reviewsCountChart.vue'
    import tabs from "../../components/tabsComponent.vue";
    import {mapActions, mapState} from "vuex";
    export default {
        name: "main",
        components: {
            trackingChart,
            visitChart,
            reviewsCountChart,
            tabs
        },
        data() {
            return {
                tabList: ['Главная', 'Количество отслеживаемых', 'Количество посещений']
            }
        },
        computed: {
            ...mapState('dynamics', ['linkClicksByStoresStatistics', 'linkClicksByDateStatistics','ratingStatistics']),
            ...mapState('review', ['reviewsDynamics']),
        },
        created() {
            this.loadReviewsDynamics()
            this.loadLinkClicksByStoresStatistics()
            this.loadLinkClicksByDateStatistics()
            this.loadRatingStatistics()
        },
        methods: {
            ...mapActions('dynamics', ['loadLinkClicksByStoresStatistics', 'loadLinkClicksByDateStatistics', 'loadRatingStatistics']),
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
            padding: 20px;
            border: 1px solid #e8ebef;
            border-radius: 8px;
            width: 49%;
            margin-bottom: 25px;
            &:hover {
                box-shadow: 0 8px 8px #0000000d,0 29px 26px #00000014;
            }
        }
        &__title {
            font-weight: 700;
            color: #26325c;
            font-size: 16px;
            margin-bottom: 20px;
        }
        &__wrapper {
            height: 250px;
            overflow-y: scroll;
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
