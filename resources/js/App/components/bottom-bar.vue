<template>
    <div class="bottom-bar">
        <div class="bottom-bar__wrapper">
            <div class="bottom-bar__item">
                <span>сравнения</span>
            </div>
            <div class="bottom-bar__item">
                <span>Истории</span>
            </div>
            <div class="bottom-bar__item">
                <span>Закладки</span>
            </div>
        </div>
    </div>

</template>


<style lang="scss" scoped>
.bottom-bar {
    display: block;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 100000;

    &__wrapper {
        border-top:3px solid  #a9a9a9;
        display: flex;
        height: 36px;
        bottom: 36px;
        margin-bottom: -36px;
        float: right;
        background-color: #fedcec;
        position: relative;
    }
    &__item {
        background-color: #eee;
        cursor: not-allowed;
        padding: 0 15px;

        & span {
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            line-height: 1;
            color: #a9a9a9;
        }
    }
}
</style>
