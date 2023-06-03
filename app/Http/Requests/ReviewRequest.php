<?php

namespace App\Http\Requests;


class ReviewRequest extends JsonApiRequest
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
            'title' => 'string|min:5|max:256',
            'plus' => 'string|min:5',
            'minus' => 'string|min:5',
            'body' => 'required|string|min:5',
            'sku_id' => 'required|numeric'
        ];
    }
}
