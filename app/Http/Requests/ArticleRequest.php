<?php

namespace App\Http\Requests;


class ArticleRequest extends MiddleRequest
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
            'title' => 'required|string|min:5',
            'preview' => 'required|string|min:5',
            'body' => 'required|string|min:10',
            'tag_ids' => 'array'
        ];
    }
}
