<?php

declare(strict_types=1);


namespace App\Http\Requests\Supplier;


use App\Http\Requests\MiddleRequest;

class StoreCreateRequest extends MiddleRequest
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
            'name' => 'required|string|min:2|max:255',
            'store_url' => 'required|string'
        ];
    }
}