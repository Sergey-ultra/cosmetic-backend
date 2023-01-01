import axios from 'axios'
//import store from '@/store'


const instance = axios.create({
    baseURL: '/api/admin' // ||'https://smart-beautiful.ru/api/admin'
})

instance.interceptors.request.use(
    config => {

        const data = JSON.parse(localStorage.getItem('userData'))
        if (data) {
            config.headers['Authorization'] = `Bearer ${data.token}`
        }
        //store.commit('notification/clearStatuses')
        return config
    },
    e => {
        console.log('request reject ', e) // TODO remove all console logs on release
        return Promise.resolve()
    }
)


instance.interceptors.response.use(
    response => {
        const status = response.status;

        if (status === 200) {
            // if (response.data.data.success === 'deleted') {
            //     /*store.commit('notification/setSuccess', {
            //         message: "Запрос обработан успешно, и в ответе нет содержимого"
            //     })*/
            // }
            // console.log(status); // TODO remove all console logs on release
        } else if (status === 201) {
           /* store.commit('notification/setSuccess', {
                message: "Ресурс был успешно создан в ответ на запрос. Заголовок Location содержит URL, указывающий на только что созданный ресурс."
            })*/
        } else if (status === 204) {
           /* store.commit('notification/setSuccess', {
                message: "Элемент успешно удален"
            })*/
            return true;
        }

        // if (response.data.message) {
        //     store.commit('notification/setSuccess', {
        //         message: response.data.message
        //     })
        // }
        return response.data;
    },
    error => {
        console.log('response reject ', error) // TODO remove all console logs on release
/*
        if (error.response.data.error.error) {
            const e = error.response.data.error.error;

            if (e.code === 404) {
                //store.commit('notification/abort404')
            } else if (e.code === 401) {
                //store.commit('auth/removeToken')
                //store.commit('notification/abort401')
            } else {
                //store.commit('notification/setError', {
                   // message: e.message,
                   // messageData: e.data
                //})
            }
        } else {
            //store.commit('notification/setError', {
               // message: 'Ошибка подключения'
           // })
        }*/
        return Promise.reject(error)
    }
)

export default instance

// 200: OK. Все сработало именно так, как и ожидалось.
// 201: Ресурс был успешно создан в ответ на POST-запрос. Заголовок Location содержит URL, указывающий на только что созданный ресурс.
// 204: Запрос обработан успешно, и в ответе нет содержимого (для запроса DELETE, например).
// 304: Ресурс не изменялся. Можно использовать закэшированную версию.
// 400: Неверный запрос. Может быть связано с разнообразными проблемами на стороне пользователя, такими как неверные JSON-данные в теле запроса, неправильные параметры действия, и т.д.
// 401: Аутентификация завершилась неудачно.
// 403: Аутентифицированному пользователю не разрешен доступ к указанной точке входа API.
// 404: Запрошенный ресурс не существует.
// 405: Метод не поддерживается. Сверьтесь со списком поддерживаемых HTTP-методов в заголовке Allow.
// 415: Не поддерживаемый тип данных. Запрашивается неправильный тип данных или номер версии.
// 422: Проверка данных завершилась неудачно (в ответе на POST-запрос, например). Подробные сообщения об ошибках смотрите в теле ответа.
// 429: Слишком много запросов. Запрос отклонен из-за превышения ограничения частоты запросов.
// 500: Внутренняя ошибка сервера. Возможная причина — ошибки в самой программе.
