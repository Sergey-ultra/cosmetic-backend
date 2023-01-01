<template>
<div class="container">
    <div class="person">
        <div class="person__title">
            <div
                class="person__title__element person-height"
                v-for="(period,index) in fillingPeriods"
                :key="index"
            >
                {{ period + ':00'}}
            </div>
        </div>
        <div class="person__wrapper">
            <div class="person__grid">
                <div
                    class="person__grid-item"
                    v-for="(period,index) in fillingPeriods"
                    :key="index"
                >
                </div>
            </div>
            <div
                v-for="(person, index) in persons"
                :key="index"
                class="person__item person-height"
            >
                <div
                    class="person__element"
                    :style="{ left: person.time[0] * 100 / 96 + '%' , width: person.time[1] * 100 / 96 + '%' }"
                    @mousedown.prevent="move(index, $event)"
                >
                    {{ person.name }}
                </div>
            </div>
        </div>
    </div>


    <div>
        <list-item
            v-for="(el, index) in arrayWithoutLastElement"
            :key="index"
            :index="index"
            :el="el"
            :duration="getDiff(index)"
        />
    </div>

</div>
</template>

<script>
    export default {
        name: "test",
        data: () => ({
            persons: [
                {
                    name: 'Andrey',
                    time: [36, 36]
                },
                {
                    name: 'Anna',
                    time: [38, 36]
                },
                {
                    name: 'Sola',
                    time: [23, 30]
                },
                {
                    name: 'Alex',
                    time: [0, 36]
                }
            ],
            array: [
                {time: 2, value: 0},
                {time: 4, value: 0},
                {time: 6, value: 0},
                {time: 8, value: 0},
                {time: 9, value: 0},
            ]
        }),
        computed:{
            fillingPeriods(){
                let periods = []
                for (let i=0;i<24;i++){
                    periods.push(i)
                }
                return periods
            },
            arrayWithoutLastElement(){
                let a = []
                this.array.forEach((el, index) => {
                    if (index < this.array.length - 1) {
                        a.push(el)
                    }
                })

                return  a
            }

        },
        created(){
            console.log(this.arrayWithoutLastElement)
        },
        methods:{
            getDiff(index){
                if (index < this.array.length - 1) {
                    return this.array[index + 1].time - this.array[index].time
                }
                return 0

            },
            getCoordsX(elem) {
                let box = elem.getBoundingClientRect();
                //возвращает координаты левого края элемента относительно viewport + отступ от края window
                return box.left + pageXOffset
            },
            move(index, event) {
                let element = event.target
                let shiftX =  event.pageX - this.getCoordsX(element)
                let parentElement = event.target.parentElement
                let parentWidthPart = parentElement.offsetWidth / 96
                let parentLeft = this.getCoordsX(parentElement)

                let getLeft = event => {
                    let left = Math.floor((event.pageX - shiftX  - parentLeft ) / parentWidthPart)

                    if (left < 0) left = 0
                    if (left > 60) left = 60

                    this.persons[index].time[0] = left
                }

                document.onmousemove = function(event){
                    getLeft(event)
                }

                //   отследить окончание переноса
                document.onmouseup = function () {
                    document.onmousemove = null
                    document.onmouseup = null
                }
            },
            getStart(){
                return null
            }
        }
    }
</script>

<style scoped>
    .person {
        margin-top:35px;
        width:100%;
        border:1px solid #000;
    }
    .person__title {
        display:flex;
    }
    .person__title__element:first-child {
        border:1px solid #000;
    }
    .person__title__element {
        border:1px solid #000;
        border-left:none;
        width: calc(100% / 24);
        text-align: center;
    }
    .person__wrapper {
        position: relative;
    }
    .person__grid {
        z-index:2;
        position: absolute;
        height: 100%;
        width: 100%;
        display: flex;
    }
    .person__grid-item {
        width: calc(100% / 24);
        height:100%;
        border-right: 1px solid #000;
    }
    .person__item {
        overflow:hidden;
        position: relative;
        border:1px solid #000;
        border-bottom:none;

    }
    .person__element {
        position:absolute;
        z-index:3;
        height:100%;
        top:0;
        background-color: #1012ff;
        opacity: 0.9;
        cursor:grab;
        padding: 0 10px;
        color: #fff;
        user-select: none;
    }

    .person-height {
        height: 30px;
    }
</style>
