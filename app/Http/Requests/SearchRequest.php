<?php

namespace App\Http\Requests;

class SearchRequest extends JsonApiRequest
{
    public function rules(): array
    {
        return [
            'search' => 'required|string',
        ];
    }
}
