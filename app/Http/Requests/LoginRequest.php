<?php

namespace App\Http\Requests;


class LoginRequest extends JsonApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|string',
            'password' => 'required|string|min:6|max:48',
        ];
    }
}
