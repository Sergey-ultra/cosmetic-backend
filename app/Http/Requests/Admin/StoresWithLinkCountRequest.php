<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\JsonApiRequest;

class StoresWithLinkCountRequest extends JsonApiRequest
{
    public function rules(): array
    {
        return [
            'parsed' => 'required|int'
        ];
    }
}
