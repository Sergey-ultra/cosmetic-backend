<template>
    <div class="position">
        <div class="tool-tip" :style="positionStyle">
            <slot name="content"></slot>
        </div>
         <slot class="slot"> </slot>
    </div>
</template>

<script>
    export default {
        name: "tool-tip",
        props:{
            position: {
                type: String,
                default: 'right'
            }
        },
        computed:{
            positionStyle() {
                if (['right', 'left'].includes(this.position)) {
                    return `top: 50%;transform: translateY(-50%);${this.position}: calc(100% + 2px);`
                } else if(['top', 'bottom'].includes(this.position)) {
                    return ` left: 50%; transform: translateX(-50%);${this.position}: calc(100% + 2px);`
                }
            }
        }
    }
</script>

<style lang="scss" scoped>
    .position {
        position: absolute;

        &:hover .tool-tip {
            display:flex;
        }
    }

    .tool-tip {
        font-size: 15px;
        z-index:100;
        white-space: nowrap;
        padding: 5px 15px;
        display:none;
        position: absolute;
        background-color: #000;
        color: #fff;
        border-radius: 8px;
    }
    .slot {
        height:100%;
        width:100%;
    }
</style>
