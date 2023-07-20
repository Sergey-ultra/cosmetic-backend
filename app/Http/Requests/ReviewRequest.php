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
        if ($this->boolean('asDraft')) {
            return [
                'title' => 'nullable|string|max:256',
                'plus' => 'nullable|string',
                'minus' => 'nullable|string',
                'sku_id' => 'required|numeric',
                'is_recommend' => 'nullable|in:0,1',
            ];
        }
        return [
            'title' => 'string|min:5|max:256',
            'plus' => 'string|min:5',
            'minus' => 'string|min:5',
            'body' => 'required',
            'sku_id' => 'required|numeric',
            'is_recommend' => 'nullable|in:0,1',
        ];
    }
}
