import api from '../utils/api'

export default {
    namespaced: true,
    state:{
        isUploading: false,
        progress: 0,
        uploadingFileUrls: []

    },
    mutations:{
        setIsUploading: (state, data) => state.isUploading = data,
        setProgress: (state, data) => state.progress = data,
        setUploadingFileUrls: (state, payload) => state.uploadingFileUrls = [...payload],

    },
    actions:{
        loadSelectedToBackend: async({ commit }, { files, entity, type, fileName }) => {
            commit('setIsUploading', true)
            if (['image', 'video'].includes(type)) {
                let form = new FormData();
                form.append('entity', entity);
                form.append('type', type);
                if (fileName) {
                    form.append('file_name', fileName);
                }

                for (let i = 0; i < files.length; i++) {
                    form.append('files[]', files[i])
                }

                const { data } = await api.post('/files', form, {
                    headers: {'Content-Type': 'multipart/form-data'},
                    onUploadProgress: e => {
                        if (e.lengthComputable) {
                            commit('setProgress', (e.loaded / e.total) * 100)
                        }
                    }
                });
                if (data && Array.isArray(data)) {
                    commit(
                        'setUploadingFileUrls',
                        data
                            .filter(item => item.status !== false)
                            .map(item => item.url)
                    );
                }



            }
            commit('setIsUploading', false)
        },
    }
}
