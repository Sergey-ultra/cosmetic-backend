<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\JsonApiRequest;

class ReviewParsingRequest extends JsonApiRequest
{
    public function rules(): array
    {
        return [
            'linkIds' => 'required|array',
            'isLoadToDb' => 'bool',
        ];
    }
}
