import api from '../utils/api'
import {defineStore} from "pinia";

export const useImageStore = defineStore({
    id: 'image',
    state: () => ({
        isUploading: false,
        progress:0,
        uploadingImageUrls:[]
    }),
    actions: {
        async loadSelectedToBackend({ files, folder }) {
            this.isUploading = true;

            let form = new FormData();
            form.append('folder', folder);

            //
            // if (!Array.isArray(files)) {
            //     files = [files]
            // }
            for (let i = 0; i < files.length; i++) {
                form.append('images[]', files[i]);
            }


            const { data } = await api.post('/images', form,  {
                headers: {'Content-Type': 'multipart/form-data' },
                onUploadProgress: e => {
                    if (e.lengthComputable) {
                        this.progress = (e.loaded / e.total) * 100;
                    }
                }
            });
            if (data) {
                this.uploadingImageUrls = [...data];
            }
            this.isUploading = false;
        },
    }
});
