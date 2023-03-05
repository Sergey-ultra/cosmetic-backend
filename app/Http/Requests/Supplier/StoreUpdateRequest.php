<?php

declare(strict_types=1);


namespace App\Http\Requests\Supplier;


use App\Http\Requests\JsonApiRequest;

class StoreUpdateRequest extends JsonApiRequest
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
            'id' => 'required|numeric',
            'name' => 'string|min:2|max:255',
            'link' => 'string',
            'file_url' => 'required|string',
            'image' => 'string'
        ];
    }
}
