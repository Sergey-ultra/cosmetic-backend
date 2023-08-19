<?php

namespace App\Http\Requests;

class RejectReviewRequest extends JsonApiRequest
{
    public function rules(): array
    {
        return [
            'reason_ids' => 'array',
        ];
    }
}
