import { createRouter, createWebHistory } from "vue-router";

const constantRoutes = [
    {
        path:'/admin',
        name:'main',
        component:() => import('../views/main/index.vue'),
    },
    {
        path:'/admin/review',
        name:'review',
        component:() => import('../views/review/index.vue'),
        meta: { title:'Отзывы' }
    },
    {
        path:'/admin/question',
        name:'question',
        component:() => import('../views/question/index.vue'),
        meta: { title:'Вопросы' }
    },
    {
        path:'/admin/comment',
        name:'comment',
        component:() => import('../views/comment/index.vue'),
        meta: { title:'Комментарии' }
    },
    {
        path:'/admin/article',
        name:'article',
        component:() => import('../views/article/index.vue'),
        meta: { title:'Статьи' }
    },
    {
        path:'/admin/article-comment',
        name:'article-comment',
        component:() => import('../views/article-comment/index.vue'),
        meta: { title:'Отзывы на статьи' }
    },
    {
        path:'/admin/article-edit/:id',
        name:'article-edit',
        component:() => import('../views/article/article-form.vue'),
        meta: { title:'Редактирование статьи' }
    },
    {
        path:'/admin/article-create',
        name:'article-create',
        component:() => import('../views/article/article-form.vue'),
        meta: { title:'Создание новой статьи' }
    },
    {
        path: '/admin/test',
        name: 'test',
        component: () => import('../views/test/test.vue')
    }
]


export default createRouter({
    history :createWebHistory(),
    scrollBehavior(to, from, savedPosition) {
        return { top: 0 }
    },
    //history :createWebHistory(process.env.BASE_URL),
    routes: constantRoutes
})



