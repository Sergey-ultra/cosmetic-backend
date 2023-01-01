<?php

declare(strict_types=1);


namespace App\Http\Requests\Supplier;


use App\Http\Requests\MiddleRequest;

class RegisterRequest  extends MiddleRequest
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
            'name' => 'required|string|min:2|max:32',
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed|min:6|max:48',
        ];
    }
}