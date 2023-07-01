<?php

declare(strict_types=1);


namespace App\Http\Requests\Supplier;


use App\Http\Requests\JsonApiRequest;
use App\Rules\CheckIsFileExist;

class StorePriceFileCreateRequest extends JsonApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'price_url' => [
                'required',
                'url',
                new CheckIsFileExist()
            ]
        ];
    }
}
