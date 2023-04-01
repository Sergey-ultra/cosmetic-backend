<template>
    <div>
        <ul class="tabs"
        >
            <li
                @click="setCurrentTab(tab)"
                v-for="(tab, index) in tabList"
               :key="index"
                class="tabs__item"
                :class="{'active': activeTab === tab}"
            >
                <span>{{ tab }}</span>
            </li>
        </ul>

        <template v-for="(tab, index) in tabList">
            <div
                :key="index"
                v-if="tab === activeTab"
            >
                <slot :name="`tabPanel-${index + 1}`" />
            </div>
        </template>
    </div>
</template>

<script>
export default {
    props: {
        tabList: {
            type: Array,
            required: true,
        },
        variant: {
            type: String,
            required: false,
            default: () => "vertical",
            validator: (value) => ["horizontal", "vertical"].includes(value),
        },
    },
    data() {
        return {
            activeTab: null,
        };
    },
    created() {
        this.activeTab = this.tabList[0]
    },
    methods: {
        setCurrentTab(tab) {
            this.activeTab = tab
        }
    }
};
</script>

<style scoped lang="scss">
.tabs {
    margin: 0;
    padding: 0;
    width: 100%;
    display:flex;
    overflow-x:auto;
    overflow-y:hidden;
    &__item {
        border-radius: 4px;
        display: flex;
        align-items: center;
        text-align: center;
        background-color: #e8e8e8;
        font-weight: 700;
        justify-content: center;
        padding: 6px 10px;
        font-size: 14px;
        line-height: 18px;
        white-space: nowrap;
        cursor: pointer;

        &:not(:first-child){
            margin-left: 12px;
        }
        & span {
            text-transform: capitalize;
            letter-spacing: .25px;
            font-size: 14px;
            line-height: 16px;
            display: block;
            font-weight: 700;
            text-decoration: none;
            user-select: none;
        }

        &.active {
            background-color: #666;
            color: #fff;
            font-size: 16px;
        }
    }
}
</style>
