<?php

namespace App\Http\Requests;


class ReviewRequest extends MiddleRequest
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
            'plus' => 'string|min:5',
            'minus' => 'string|min:5',
            'comment' => 'required|string|min:5',
            'sku_id' => 'required|numeric'
        ];
    }
}
