<template>
    <nav aria-label="Page navigation">
        {{perPage *(currentPage -1) + 1}} - {{ perPage * currentPage }} из {{ total }}

        Rows per page:<select v-model="perPage">
            <option
                v-for="(option, index) in options"
                :key="index"
            >
                {{ option }}
            </option>
        </select>

        <button @click="minus">Prev</button>
        <select v-model="currentPage">
            <option
                v-for="(page, index) in pages"
                :key="index"
            >
                {{ page }}
            </option>
        </select>
        <button @click="plus">Next</button>
        <!-- <ul class="pagination">
            <li class="page-item"
                v-for="(link,index) in links"
                :key="index"
                :class="{ active: link.active, disabled: !link.url }"
                @click="$emit('fetch', {page:link.url})"
            >
                <div class="page-link">{{ link.label }}</div>
            </li>
        </ul>-->
    </nav>
</template>

<script>
    export default {
        name: "pagination",
        props:{
            links: Array,
            total: Number,
            perPage: Number,
            currentPage:Number,
            lastPage: Number
        },
        data(){
            return {
                options:[5,10,20,50,100],
            }
        },
        computed:{
            pages(){
                return Array.from({length: this.lastPage}, (v, k) => k+1)
            }
        },
        watch:{
            perPage(){
                this.currentPage = 1
                this.$emit('fetch',{currentPage:this.currentPage, perPage:this.perPage})
            },
            currentPage(){
                this.$emit('fetch',{currentPage:this.currentPage, perPage:this.perPage})
            }
        },
        methods:{
            plus(){
                if (this.currentPage < this.lastPage) this.currentPage++
            },
            minus(){
                if (this.currentPage > 1) this.currentPage--

            }
        }
    }
</script>

<style scoped>

</style>
