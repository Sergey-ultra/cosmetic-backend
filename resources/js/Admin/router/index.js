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
        path:'/admin/video',
        name:'video',
        component:() => import('../views/video/index.vue'),
        meta: { title:'Видео' }
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
    },
    {
        path:'/admin/parser-link',
        name:'link-parser',
        component:() => import('../views/parser-link/index.vue'),
        meta: { title:'Парсинг ссылок на товарные предложения' }
    },
    {
        path:'/admin/parser-product',
        name:'product-parser',
        component:() => import('../views/parser-product/index.vue'),
        meta: { title:'Парсинг товарных предложений' }
    },
    {
        path:'/admin/parser-price',
        name:'price-parser',
        component:() => import('../views/parser-price/index.vue'),
        meta: { title:'Парсинг цен' }
    },
    {
        path:'/admin/parser-review-link',
        name:'review-link-parser',
        component:() => import('../views/parser-review-link/index.vue'),
        meta: { title:'Парсинг ссылок на отзывы' }
    },
    {
        path:'/admin/parser-review',
        name:'review-parser',
        component:() => import('../views/parser-review/index.vue'),
        meta: { title:'Парсинг отзывов' }
    },
    {
        path:'/admin/parser-review-publishing',
        name: 'review-publishing-list',
        component:() => import('../views/parser-review-publishing/index.vue'),
        meta: { title:'Публикация отзывов' }
    },
    {
        path:'/admin/parser-review-publishing/:id',
        name:'review-publishing',
        component:() => import('../views/parser-review-publishing/item.vue'),
        meta: { title:'Публикация отзыва' }
    },
    {
        path: '/admin/settings',
        name: 'settings',
        component:() => import('../views/settings/index.vue'),
        meta: { title:'Настройки' }
    },
    {
        path:'/admin/tag',
        name:'tag',
        component:() => import('../views/tag/index.vue'),
        meta: { title:'Теги статей' }
    },
    {
        path:'/admin/store',
        name:'store',
        component:() => import('../views/store/index.vue'),
        meta: { title:'Магазины' }
    },
    {
        path:'/admin/supplier',
        name:'supplier',
        component:() => import('../views/supplier/index.vue'),
        meta: { title:'Магазины поставщиков' }
    },
    {
        path:'/admin/countries',
        name:'countries',
        component:() => import('../views/country/index.vue'),
        meta: { title:'Страны' }
    },
    {
        path:'/admin/ingredients',
        name:'ingredients',
        component:() => import('../views/ingredient/index.vue'),
        meta: { title:'Ингредиенты' }
    },
    {
        path:'/admin/brands',
        name:'brands',
        component:() => import('../views/brand/index.vue'),
        meta: { title: 'Бренды' }
    },
    {
        path:'/admin/categories',
        name:'categories',
        component:() => import('../views/category/index.vue'),
        meta: { title: 'Категории' }
    },
    {
        path:'/admin/skus',
        name:'skus',
        component:() => import('../views/sku/index.vue'),
        meta: {title: 'Товарные предложения'}
    },
    {
        path:'/admin/users',
        name:'users',
        component:() => import('../views/users/index.vue'),
        meta: {title: 'Пользователи'}
    },
    {
        path:'/admin/trackings',
        name:'trackings',
        component:() => import('../views/tracking/index.vue'),
        meta: {title: 'Отслеживаемые'}
    },
    {
        path:'/admin/messages',
        name:'messages',
        component:() => import('../views/message/index.vue'),
        meta: {title: 'Сообщения'}
    },
]


export default createRouter({
    history :createWebHistory(),
    scrollBehavior(to, from, savedPosition) {
        return { top: 0 }
    },
    //history :createWebHistory(process.env.BASE_URL),
    routes: constantRoutes
})



