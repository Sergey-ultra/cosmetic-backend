<?php

namespace App\Http\Requests;



class FileRequest extends JsonApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'files' => 'required|array',
            'files.*' => 'file',
            'entity' => 'string|in:sku,review,article,article-ckeditor,avatar',
            'type' => 'string|in:image,video',
            'file_name' => 'string'
        ];
    }
}
