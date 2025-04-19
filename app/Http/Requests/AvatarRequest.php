<?php

namespace App\Http\Requests;

class AvatarRequest extends JsonApiRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'avatar' => 'required|string',
        ];
    }
}
