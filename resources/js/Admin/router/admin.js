
export default [
    {
        path:'/admin/link-parser',
        name:'link-parser',
        component:() => import('../views/parser/link-parser/link-parser.vue'),
        meta: { title:'Парсинг ссылок на товарные предложения' }
    },
    {
        path:'/admin/product-parser',
        name:'product-parser',
        component:() => import('../views/parser/product-parser/product-parser.vue'),
        meta: { title:'Парсинг товарных предложений' }
    },
    {
        path:'/admin/price-parser',
        name:'price-parser',
        component:() => import('../views/parser/price-parser/price-parser.vue'),
        meta: { title:'Парсинг цен' }
    },
    {
        path:'/admin/settings',
        name:'settings',
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
        component:() => import('../views/store/stores.vue'),
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
        component:() => import('../views/country/country.vue'),
        meta: { title:'Страны' }
    },
    {
        path:'/admin/ingredients',
        name:'ingredients',
        component:() => import('../views/ingredient/ingredients.vue'),
        meta: { title:'Ингредиенты' }
    },
    {
        path:'/admin/brands',
        name:'brands',
        component:() => import('../views/brand/brands.vue'),
        meta: { title: 'Бренды' }
    },
    {
        path:'/admin/categories',
        name:'categories',
        component:() => import('../views/category/categories.vue'),
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
        component:() => import('../views/users/users.vue'),
        meta: {title: 'Пользователи'}
    },
    {
        path:'/admin/trackings',
        name:'trackings',
        component:() => import('../views/tracking/index.vue'),
        meta: {title: 'Отслеживаемые'}
    },
]
