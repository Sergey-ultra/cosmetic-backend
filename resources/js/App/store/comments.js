import api from '../utils/api'
import {defineStore} from "pinia";
import {useNotificationStore} from "./notification";
const notificationStore = useNotificationStore()

export const useCommentStore = defineStore({
    id: 'comment',
    state: () => ({
        comments: [],
        myComments: [],
        isLoadingMyComments: false
    }),
    actions: {
        async loadMyComments()  {
            this.isLoadingMyComments = true;
            const { data } = await api.get('/comments/my');
            if (data) {
                this.myComments = [...data];
            }
            this.isLoadingMyComments = false;
        },
        async loadComments(review_id) {
            const { data } = await api.get('/comments', {params: {review_id}});
            if (data) {
                this.comments = [...data];
            }
        },
        async sendComment(object) {
            const { data } = await api.post('/comments', object);
            if (data.status) {
                notificationStore.setSuccess('Комментарий успешно создан и будет опубликован после модерации');
            }
        },
        async deleteItem(id) {
            await api.delete(`/comments/${id}`);
            await this.loadMyComments();
            notificationStore.setSuccess('Комментарий успешно удален');
        }
    },
});
