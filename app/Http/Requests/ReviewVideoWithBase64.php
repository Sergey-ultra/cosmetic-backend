<?php


namespace App\Http\Requests;


class ReviewVideoWithBase64 extends JsonApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'file' => 'required|string',
            'description' => 'required|string|min:5',
            'sku_id' => 'required|numeric'
        ];
    }
}
