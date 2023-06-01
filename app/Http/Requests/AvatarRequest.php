<?php

namespace App\Http\Requests;

class AvatarRequest extends JsonApiRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

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
