<template>
    <div class="chart" ref="chartdiv">
    </div>
</template>

<script setup>
    import * as am4core from "@amcharts/amcharts4/core";
    import * as am4charts from "@amcharts/amcharts4/charts";
    import am4themes_animated from "@amcharts/amcharts4/themes/animated";
    import {ref, defineProps, onMounted, watch, onUnmounted, computed} from "vue";

    am4core.useTheme(am4themes_animated);

    const props = defineProps({
        chartData: {
            type: Array,
            default: () => [{
                date: 0,
                avg: 0,
                min: 0,
                max: 0,
            }]
        }
    });

    let chart = ref('');
    let chartdiv = ref (null);

    const preparedData = computed(() => {
        return props.chartData.map(el => {
            return {
                date: el.date,
                name: "name" + el.date,
                valueAvg: el.avg,
                valueMin: el.min,
                valueMax: el.max,
            }
        })
    });

    watch(preparedData, value => {
            if (value.length) {
                renderChart();
            }
        }
    );

    onMounted(() => renderChart());
    onUnmounted(() => {
        if (chart.value) {
            chart.value.dispose();
        }
    });

    const renderChart = () => {
        let chart = am4core.create(chartdiv.value, am4charts.XYChart);

        chart.paddingRight = 20;


        chart.data =  preparedData.value;

        chart.dateFormatter.inputDateFormat = "yyyy-MM-dd";

        let dateAxis = chart.xAxes.push(new am4charts.DateAxis());
        dateAxis.renderer.grid.template.location = 0;

        let valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.tooltip.disabled = true;
        valueAxis.renderer.minWidth = 35;

        let series1 = chart.series.push(new am4charts.LineSeries());
        series1.dataFields.dateX = "date";
        series1.dataFields.valueY = "valueAvg";

        series1.tooltipText = "средняя";
        chart.cursor = new am4charts.XYCursor();

        let bullet = series1.bullets.push(new am4charts.CircleBullet());
        bullet.circle.strokeWidth = 2;
        bullet.circle.radius = 4;
        bullet.circle.fill = am4core.color("#fff");

        let bullethover = bullet.states.create("hover");
        bullethover.properties.scale = 1.3;


        let series2 = chart.series.push(new am4charts.LineSeries());
        series2.dataFields.dateX = "date";
        series2.dataFields.valueY = "valueMin";

        series2.tooltipText = "минимальная";
        chart.cursor = new am4charts.XYCursor();

        let bullet2 = series2.bullets.push(new am4charts.CircleBullet());
        bullet.circle.strokeWidth = 2;
        bullet.circle.radius = 4;
        bullet.circle.fill = am4core.color("#fff");

        let bullethover2 = bullet2.states.create("hover");
        bullethover2.properties.scale = 1.3;


        // let scrollbarX = new am4charts.XYChartScrollbar();
        // scrollbarX.series.push(series1);
        // chart.scrollbarX = scrollbarX;

        chart.value = chart;
    };
</script>

<style scoped lang="scss">
    .chart {
        width: 100%;
        height: 300px;
    }

    @media (max-width: 700px) {
        .chart {
            height: 250px;
        }
    }
    @media (max-width: 500px) {
        .chart {
            height: 200px;
        }
    }
</style>
