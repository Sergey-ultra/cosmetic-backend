<?php

namespace App\Http\Requests;


class ArticleRequest extends JsonApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|min:5',
            'preview' => 'required|string|min:5',
            'body' => 'required|string|min:10',
            'tag_ids' => 'array'
        ];
    }
}
