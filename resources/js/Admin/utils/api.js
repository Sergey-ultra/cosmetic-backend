import axios from 'axios'
import store from '../store'


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
        // console.log('request reject ', e) // TODO remove all console logs on release
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
        if (error.response.data) {
            const data = error.response.data

            console.log(data);
            const status = error.response.status;

            if (status === 401) {
                //store.commit('auth/removeToken')
                //store.commit('notification/abort401')
            } else if (status === 419) {

            } else {
                store.dispatch('notification/setError', {
                    message:data.message,
                    file: data.file,
                    line: data.line
                });
            }
        } else {
            store.dispatch('notification/setError', { message: 'Ошибка подключения' })
        }
        return {};
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
