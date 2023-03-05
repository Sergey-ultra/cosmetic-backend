<?php

namespace App\Http\Requests;

class ReviewVideoWithUrlsRequest extends JsonApiRequest
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
            'file_path' => 'required|string',
            'thumbnail_path' => 'required|string',
            'description' => 'required|string|min:5',
            'sku_id' => 'required|numeric'
        ];
    }
}
