<?php

namespace App\Http\Requests;

class ChangePasswordRequest extends JsonApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'password' => 'required|string|min:6|max:48',
            'new_password' => 'required|string|min:6|max:48',
        ];
    }
}
