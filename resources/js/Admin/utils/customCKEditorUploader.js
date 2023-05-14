import api from './api'

export default class UploadAdapter
{
    constructor (loader) {
        this.loader = loader;
    }

    uploadFile(file, fileName = null){
        let form= new FormData();
        form.append('files[]', file);
        form.append('entity', 'article-ckeditor');
        form.append('type', 'image');
        if (fileName) {
            form.append('file_name', fileName);
        }


        const config = { headers: {'Content-Type': 'multipart/form-data' }}
        return api.post('/files', form, config);
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
                            console.log({ default: resp.data[0].url })
                            resolve({ default: resp.data[0].url })
                        })
                })
            } )
    }

    // Aborts the upload process.
    abort () {
        console.log('Abort')
    }
}

