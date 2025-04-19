<?php

namespace App\Http\Requests;

class ChargeRequest extends JsonApiRequest
{
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:10',
            'wallet_id' => 'required|numeric',
        ];
    }
}
