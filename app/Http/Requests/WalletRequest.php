<?php

namespace App\Http\Requests;

class WalletRequest extends JsonApiRequest
{
    public function rules(): array
    {
        return [
            'to' => 'string',
        ];
    }
}
