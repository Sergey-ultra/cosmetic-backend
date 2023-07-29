<?php

namespace App\Http\Requests;

class SkuRequest extends JsonApiRequest
{
    public function rules(): array
    {
        return [
            'category_id' => 'required|numeric',
            'brand_id' => 'required|numeric',
            'name' => 'required|string|min:5',
            'description' => 'required|string|min:5',
            'volume' => 'required|string',
            'images' => 'required|array',
        ];
    }
}
