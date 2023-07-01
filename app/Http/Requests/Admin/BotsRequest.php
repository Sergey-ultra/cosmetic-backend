<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\JsonApiRequest;

class BotsRequest extends JsonApiRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'bots' => 'array',
            'bots.*.email' => 'required|string|email',
            'bots.*.name' => 'required|string|min:2|max:32',
            'bots.*.password' => 'required|string|min:6|max:48',
        ];
    }
}
