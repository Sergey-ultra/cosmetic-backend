import { createRouter, createWebHistory } from "vue-router";
import auth from './middleware/auth';
import {useAuthStore} from "../store/auth";
import {useProductStore} from "../store/product";




const router =  createRouter({
    history :createWebHistory(),
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        }
        if (to.params.savePosition) {
            return {}
        }

        const productStore = useProductStore();
        if (productStore.isLoadMore) {
            return {};
        }

        return { top: 0, left: 0 }
    },
    routes: [
        {
            path:'/:pathMatch(.*)*',
            name:'404',
            meta: { title: 'Не найдено'},
            component:() => import('../views/404'),
        },
        {
            path:'/401',
            name:'401',
            meta: { title: 'Не авторизован'},
            component:() => import('../views/401'),
        },
        {
            path: '',
            name:'main',
            meta: { title: 'Smart-Beautiful - агрегатор цен косметических товаров'},
            component:() => import('../views/index'),
            //meta: { middleware: [auth] }
        },
        {
            path:'/social-callback',
            name:'social-callback',
            meta: { title: 'Регистрация через сервисы'},
            component:() => import('../views/social-callback'),
        },
        {
            path:'/about',
            name:'about',
            meta: { title: 'О нас'},
            component:() => import('../views/about'),
        },
        {
            path:'/article/:id',
            name:'article',
            meta: { title: 'Статья'},
            component:() => import('../views/article'),
        },
        {
            path:'/policy',
            name:'policy',
            meta: { title: 'Политика конфидициальности'},
            component:() => import('../views/policy'),
        },
        {
            path: '/search',
            name: 'search',
            meta: { title: 'Поиск'},
            component: () => import ('../views/search'),
            props: true
        },
        {
            path:'/comparison',
            name:'comparison',
            meta: { title: 'Сравнение товаров'},
            component:() => import('../views/comparison'),
        },
        {
            path:'/categories',
            name:'categories',
            meta: { title: 'Категории'},
            component:() => import('../views/categories'),
        },
        {
            path: '/brands',
            name: 'brands',
            meta: { title: 'Бренды'},
            component: () => import ('../views/brand'),
            //props: true,
            //children:
        },
        {
            path: '/brand/:brand_code',
            name: 'brand',
            meta: { title: 'Бренд'},
            component: () => import ('../views/brand/brand'),
            props: true
        },
        {
            path:'/catalog/:category_code',
            name: 'category',
            meta: { title: 'Категория'},
            component: () => import('../views/category'),
            props: true,

        },
        {
            path: '/reviews/:product_code',
            name: 'reviews',
            meta: { title: 'Отзывы товара'},
            component: () => import('../views/reviews')
        },
        {
            path: '/add-review/:product_code',
            name: 'add-review',
            meta: {title: 'Добавить отзывы товара'},
            component: () => import('../views/add-review')
        },
        {
            path: '/add-photos/:product_code',
            name: 'add-photos',
            meta: {
                title: 'Добавить новые фотографии о товаре',
                middleware: auth
            },
            component: () => import('../views/add-new-photos')
        },
        {
            path: '/questions/:product_code',
            name: 'questions',
            meta: { title: 'Вопросы товара'},
            component: () => import('../views/questions')
        },
        {
            path: '/product/:product_code',
            name: 'product',
            component:() => import('../views/product'),
            props:true,
            children: [
                {
                    path: '',
                    name: 'description',
                    meta: { title: 'Описание товара'},
                    component: () => import('../views/product/description')
                },
                {
                    path: 'price',
                    name: 'price',
                    meta: { title: 'Цены товара'},
                    component: () => import('../views/product/prices')
                },
            ]
        },
        {
            path: '/edit-profile',
            name: 'editProfile',
            meta: { title: 'Редактирование профиля', auth: true},
            component: () => import('../views/edit-profile')
        },
        {
            path: '/favorites',
            name: 'favorites',
            meta: { title: 'Избранное', auth: true},
            component: () =>  import('../views/favorites'),

        },
        {
            path: '/profile',
            name: 'profile',
            component: () => import('../views/profile'),
            meta:{ title: 'Профиль', auth: true },
            children: [
                {
                    path: 'viewed-items',
                    name: 'viewedItems',
                    meta: { title: 'Просмотренные', auth: true},
                    component: () =>  import('../views/profile/viewed-items')
                },
                {
                    path: 'reviews',
                    name: 'myReviews',
                    meta: { title: 'Мои публикации', auth: true},
                    component: () => import('../views/profile/my-reviews')
                },
                {
                    path: 'comments',
                    name: 'myComments',
                    meta: { title: 'Мои комментарии', auth: true},
                    component: () => import('../views/profile/my-comments')
                },
                {
                    path: 'answers',
                    name: 'myAnswers',
                    meta: { title: 'Мои ответы', auth: true},
                    component: () => import('../views/profile/my-answers')
                }
            ]
        },
    ],
})

router.beforeEach((to, from, next) => {
    document.title = to.meta.title;
    //метрика яндекса
    window.ym(87589913, 'hit', to)

    // if (messages[$route.query.message]) {
    //
    // }
    //console.log(store.state.auth.isAuth)

    const middleware = to.meta.middleware
    if (!middleware) {
        return next()
    }
    const authStore = useAuthStore();
    const context = { to, from, next, authStore }
    return middleware({...context})
})


export default router
