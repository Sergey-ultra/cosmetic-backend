<?php

declare(strict_types=1);


namespace App\Http\Requests\Supplier;


use App\Http\Requests\MiddleRequest;

class AdditionalInfoRequest extends MiddleRequest
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
            'store_url' => 'required|string',
            'price_url' => 'required|string',
            'fio' => 'required|string',
            'email' => 'required|email|string',
            'phone' => 'required|regex:#[0-9]{9}#',
        ];
    }
}