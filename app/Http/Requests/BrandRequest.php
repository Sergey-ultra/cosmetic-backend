<?php

namespace App\Http\Requests;

class BrandRequest extends JsonApiRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
        ];
    }
}
