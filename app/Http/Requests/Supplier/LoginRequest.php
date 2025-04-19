<?php

declare(strict_types=1);

namespace App\Http\Requests\Supplier;

use App\Http\Requests\JsonApiRequest;


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
            'email' => 'required|string|email',
            'password' => 'required|string|min:6|max:48'
        ];
    }
}
