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
    margin: 32px 0;
    padding: 0;
    width: 100%;
    display:flex;
    overflow-x:auto;
    overflow-y:hidden;
    border-bottom: 1px solid #e8ebef;
    &__item {
        color: #83899f;
        display: flex;
        align-items: center;
        text-align: center;
        font-weight: 700;
        justify-content: center;
        padding: 6px 10px;
        font-size: 16px;
        line-height: 20px;
        white-space: nowrap;
        border-bottom: 1px solid rgba(0,0,0,0);
        cursor: pointer;

        .tabs__item + .tabs__item {
            margin-left: 24px;
        }
        & span {
            text-transform: capitalize;
            letter-spacing: .25px;
            display: block;
            font-weight: 700;
            text-decoration: none;
            user-select: none;
        }

        &.active {
            color: #26325c;
            font-size: 18px;
            border-color: #454cee;
        }
    }
}
</style>
