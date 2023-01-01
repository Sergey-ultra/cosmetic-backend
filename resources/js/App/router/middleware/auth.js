export default function auth ({ next, from, authStore, to }) {
    if(!authStore.isAuth) {
        return next({
            name: '401',
            query : { to: to.name, from: from.fullPath }
        })
    }
    return next()
}