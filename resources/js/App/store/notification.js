import {defineStore} from "pinia";

export const useNotificationStore = defineStore({
    id: 'notification',
    state: () => ({
        notes: []
    }),
    actions: {
        setNote(object) {
            this.notes.unshift(object);
        },
        clearNote() {
            this.notes.splice(this.notes.length - 1, 1);
        },
        deleteNote(index) {
            this.notes.splice(index, 1);
        },
        setSuccess(message) {
            this.setNote({ status: 'success', message });
            setTimeout(() => {
                this.clearNote();
            }, 5000);
        },
        setError(message) {
            this.setNote({ status: 'error', message });
            setTimeout(() => {
                this.clearNote();
            }, 5000);
        }
    }
});
