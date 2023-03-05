<?php

declare(strict_types=1);

namespace App\Http\Requests\Supplier;

use App\Http\Requests\JsonApiRequest;


class LoginRequest extends JsonApiRequest
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
            'email' => 'required|string|email',
            'password' => 'required|string|min:6|max:48'
        ];
    }
}
