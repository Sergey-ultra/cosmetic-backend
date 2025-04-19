<template>
    <div class="table-block">
        <link-table v-model:isReloadLinks="isReloadLinksLocal"/>
    </div>
</template>

<script>
import {mapMutations, mapState} from "vuex";
import linkTable from "./src/link-table.vue"
import buttonComponent from "../../components/button-component.vue"


export default {
    name: "product-parser",
    components: {
        linkTable,
        buttonComponent
    },
    data() {
        return {

        }
    },
    computed: {
        ...mapState('reviewParser', ['isParsingLinks', 'isReloadLinks', 'preview', 'isShowPreview', 'isOpenPreviewAfterParsing']),
        isReloadLinksLocal:{
            get() {
                return this.isReloadLinks
            },
            set(value) {
                this.setIsReloadLinks(value)
            }
        },
        isShowPreviewLocal: {
            get() {
                return this.isShowPreview
            },
            set(value) {
                this.setIsShowPreview(value)
            }
        },
    },
    methods: {
        ...mapMutations('reviewParser', ['setIsReloadLinks', 'setIsShowPreview', 'setIsOpenPreviewAfterParsing']),
    }
}
</script>

<style scoped lang="scss">
@import '../../../../css/admin/form';
select option {
    display: flex;
    justify-content: space-between;
    width: 200px;

    & div {
        padding: 0 10px;
    }
}
.option__not-empty {
    background: #84f4bd;
}
.button-settings {
    margin-top: 20px;
    margin-left: auto;
}




label {
    cursor:pointer;
    user-select: none;
    &:not(:last-child) {
        margin-right: 20px;
    }
}

.target__url {
    width:500px;
}
.parser {
    display: flex;
    flex-direction:column;
    &__settings {
        margin-top: 25px;
        display:flex;
        justify-content: space-between;
    }
}


.field {
    display:flex;
    align-items:flex-end;
    &__row {
        display:flex;
        margin-top:10px;

    }
    &__item {
        &:not(last-child) {
            margin-right:15px;
        }
        &-img {
            min-width:200px;

        }
        &-tag {
            width:400px;
        }
        &-number {
            width:150px;
        }
        &-is-required {
            width: 40px;
        }
    }
}

.svg-loader{
    height: 100%;
    display:flex;
    position: relative;
    align-content: space-around;
    justify-content: center;
}
.loader-svg{
    position: absolute;
    left: 0; right: 0; top: 0; bottom: 0;
    fill: none;
    stroke-width: 5px;
    stroke-linecap: round;
    stroke: rgb(64, 0, 148);
}
.loader-svg.bg{
    stroke-width: 8px;
    stroke: rgb(207, 205, 245);
}


.add__field,
.delete__field {
    display:flex;
    align-items:center;
    justify-content: center;
    margin-left:15px;
    padding: 0 10px;
    background-color: #fff;
    border:none;
    height:25px;
    box-shadow: 1px 0 0 #f3c8d2, -1px 0 0 #f3c8d2, 0 -1px 0 rgba(243,200,210,0.5), 0 4px 7px rgba(243,200,210,0.4), 0 1px 1px #f3c8d2;
}

.table-block {
    position:relative;
}
.table__layer {
    position: absolute;
    top:0;
    bottom:0;
    left:0;
    right:0;
    background-color: rgba(255, 255, 255, 0.81);
}
</style>
