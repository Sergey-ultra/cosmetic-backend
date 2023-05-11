<?php

namespace App\Http\Requests;

class ChangePasswordRequest extends JsonApiRequest
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
            'password' => 'required|string|min:6|max:48',
            'new_password' => 'required|string|min:6|max:48',
        ];
    }
}