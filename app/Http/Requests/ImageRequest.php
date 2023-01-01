<?php

namespace App\Http\Requests;



class ImageRequest extends MiddleRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'file_name.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'folder' => 'string'
        ];
    }
}
