<template>
    <div class="chart" ref="reviewCount"></div>
</template>

<script>
    import * as am4core from "@amcharts/amcharts4/core";
    import * as am4charts from "@amcharts/amcharts4/charts";
    import am4themes_animated from "@amcharts/amcharts4/themes/animated";

    am4core.useTheme(am4themes_animated);
    export default {
        name: "reviewsCountChart",
        props: {
            chartData: {
                type:Array,
                default: () => [{
                    count: 0,
                    date: 0,
                }]
            }
        },
        data() {
            return {
                chart: '',
            }
        },
        beforeDestroy() {
            if (this.chart) {
                console.log('time')
                this.chart.dispose();
            }
        },
        computed: {
            preparedData() {

                return this.chartData.map(el => {
                    return {
                        date: el.date,
                        name: "name" + el.date,
                        count: el.count,
                    }
                })
            }
        },
        watch: {
            preparedData(value) {
                if (value.length) {
                    // console.log(value)
                    // this.chart.data = value

                    this.renderChart()
                }
                //this.chart.invalidateData()
            }
        },
        mounted() {
            this.renderChart()
        },
        methods: {
            renderChart() {
                let trackingChart = am4core.create(this.$refs.reviewCount, am4charts.XYChart);

                trackingChart.paddingRight = 20;


                trackingChart.data = this.preparedData;

                trackingChart.dateFormatter.inputDateFormat = "yyyy-MM-dd";

                let dateAxis = trackingChart.xAxes.push(new am4charts.DateAxis());
                dateAxis.renderer.grid.template.location = 0;

                let valueAxis = trackingChart.yAxes.push(new am4charts.ValueAxis());
                valueAxis.tooltip.disabled = true;
                valueAxis.renderer.minWidth = 35;

                let series1 = trackingChart.series.push(new am4charts.LineSeries());
                series1.dataFields.dateX = "date";
                series1.dataFields.valueY = "count";

                series1.tooltipText = "{valueY.name}";
                trackingChart.cursor = new am4charts.XYCursor();

                let bullet = series1.bullets.push(new am4charts.CircleBullet());
                bullet.circle.strokeWidth = 2;
                bullet.circle.radius = 4;
                bullet.circle.fill = am4core.color("#fff");

                let bullethover = bullet.states.create("hover");
                bullethover.properties.scale = 1.3;



                // let scrollbarX = new am4charts.XYChartScrollbar();
                // scrollbarX.series.push(series1);
                // chart.scrollbarX = scrollbarX;

                this.chart = trackingChart;
            }
        }
    }
</script>

<style scoped>

</style>