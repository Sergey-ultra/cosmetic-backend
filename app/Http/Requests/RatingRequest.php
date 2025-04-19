<?php

namespace App\Http\Requests;


class RatingRequest extends JsonApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'rating' => 'required|in:1,2,3,4,5',
            'sku_id' => 'required|numeric'
        ];
    }
}
