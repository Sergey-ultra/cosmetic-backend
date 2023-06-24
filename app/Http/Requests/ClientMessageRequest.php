<?php

namespace App\Http\Requests;

class ClientMessageRequest extends JsonApiRequest
{
    public function rules(): array
    {
        return [
            'message' => 'required|string|min:10',
            'author' => 'required|string|min:2',
        ];
    }
}
