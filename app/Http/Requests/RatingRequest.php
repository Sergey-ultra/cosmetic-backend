<?php

namespace App\Http\Requests;


class RatingRequest extends MiddleRequest
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
            'rating' => 'required|in:1,2,3,4,5',
            'sku_id' => 'required|numeric'
        ];
    }
}
