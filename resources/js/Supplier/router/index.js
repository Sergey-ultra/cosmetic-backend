import { createRouter, createWebHistory } from "vue-router";
import store from '../store'



const router =  createRouter({
    history :createWebHistory(),
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        }
        return { top: 0, left: 0 }
    },
    routes: [
        {
            path:'/supplier',
            name:'store',
            meta: { title: 'Магазин'},
            component:() => import('../views/store/index.vue'),
        },
        {
            path:'/supplier/edit',
            name:'edit-store',
            meta: { title: 'Редактирование магазина'},
            component:() => import('../views/edit-store/index.vue'),
        },
        {
            path:'/supplier/edit-profile',
            name:'editProfile',
            meta: { title: 'Редактирование профиля'},
            component:() => import('../views/profile/edit.vue'),
        }
    ],
})

router.beforeEach((to, from, next) => {
    const isAuth = store.state.auth.isAuth
    const requireAuth = to.matched.some(record => record.meta.auth)
    if (requireAuth && !isAuth) {

        next('/')
    } else {
        next()
    }

})


export default router
