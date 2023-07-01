<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\JsonApiRequest;

class ProductParsingRequest extends JsonApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'storeId' => 'required|int',
            'linkIds' => 'required|array',
            'isLoadToDb' => 'bool',
            'isInsertIngredients' => 'bool',
            'brandId' => 'nullable|int',
        ];
    }
}
