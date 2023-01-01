<?php

declare(strict_types=1);

namespace App\Http\Requests\Supplier;

use App\Http\Requests\MiddleRequest;


class LoginRequest extends MiddleRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6|max:48'
        ];
    }
}
