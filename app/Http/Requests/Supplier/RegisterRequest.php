<?php

declare(strict_types=1);


namespace App\Http\Requests\Supplier;


use App\Http\Requests\JsonApiRequest;

class RegisterRequest  extends JsonApiRequest
{
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
            'password' => 'required|string|confirmed|min:6|max:48',
        ];
    }
}
