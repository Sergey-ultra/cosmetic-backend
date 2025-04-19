<?php

namespace App\Http\Requests\Admin\Parser;

use App\Http\Requests\JsonApiRequest;

class HourCountRequest extends JsonApiRequest
{
    public function rules(): array
    {
        return [
            'hour_count' => 'required|int'
        ];
    }
}
