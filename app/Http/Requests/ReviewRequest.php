<?php

namespace App\Http\Requests;


class ReviewRequest extends JsonApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'string|min:5|max:256',
            'plus' => 'string|min:5',
            'minus' => 'string|min:5',
            'body' => 'required|string|min:5',
            'sku_id' => 'required|numeric',
            'images' => 'array',
            'anonymously' => 'required|in:0,1',
            'is_recommend' => 'in:0,1',
        ];
    }
}
