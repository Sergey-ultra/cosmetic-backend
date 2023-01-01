import api from '../utils/api'

export default {
    namespaced: true,
    state:{
        isUploading: false,
        progress:0,
        uploadingImageUrls:[]

    },
    mutations:{
        setIsUploading: (state, data) => state.isUploading = data,
        setProgress: (state, data) => state.progress = data,
        setUploadingImageUrls: (state, payload) => state.uploadingImageUrls = [...payload],

    },
    actions:{
        loadSelectedToBackend: async({ commit }, { files, folder }) => {
            commit('setIsUploading', true)

            let form = new FormData()
            form.append('folder', folder)

            // if (!Array.isArray(files)) {
            //     files = [files]
            // }

            for (let i = 0; i < files.length; i++) {
                console.log(files[i])
                form.append('images[]', files[i])
            }



            const { data } = await api.post('/images', form,  {
                headers: {'Content-Type': 'multipart/form-data' },
                onUploadProgress: e => {
                    if (e.lengthComputable) {
                        commit('setProgress', (e.loaded / e.total) * 100)
                    }
                }
            })
            if (data) {
                commit('setUploadingImageUrls', data)
            }
            commit('setIsUploading', false)
        },
    }
}
