<?php

namespace App\Http\Requests;


class RegisterRequest extends JsonApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:32',
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed|min:6|max:48'
        ];
    }
}
