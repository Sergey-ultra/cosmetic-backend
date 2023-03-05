<?php

namespace App\Http\Requests;



class FileRequest extends JsonApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

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
            'entity' => 'string|in:sku,review',
            'type' => 'string|in:image,video',
            'file_name' => 'string'
        ];
    }
}
