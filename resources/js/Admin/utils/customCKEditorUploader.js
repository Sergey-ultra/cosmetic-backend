import api from './api'

export default class UploadAdapter
{
    constructor (loader) {
        this.loader = loader;
    }

    uploadFile(file){
        let formData= new FormData();
        formData.append('images[]', file);
        formData.append('folder', 'articles/ckEditor')

        const config = { headers: {'Content-Type': 'multipart/form-data' }}
        return api.post('/images', formData, config);
    }


    upload () {
        /*return new Promise((resolve, reject) => {
            const reader = new window.FileReader();
            console.log('здесь')
            reader.addEventListener('load', () => {
                console.log('пиздец')
                resolve({'default': reader.result});
            });

            reader.addEventListener('error', err => {
                reject(err);
            });

            reader.addEventListener('abort', () => {
                reject();
            });

            this.loader.file.then(file => {
                console.log('пиздец')
                reader.readAsDataURL(file);
            });
        });*/



            return new Promise( ( resolve, reject ) => {
                this.loader.file.then(file => {
                    this.uploadFile(file)
                        .then(resp => {
                            console.log({ default: resp.data[0] })
                            resolve({ default: resp.data[0] })
                        })
                })
            } )
    }

    // Aborts the upload process.
    abort () {
        console.log('Abort')
    }
}

