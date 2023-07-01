<?php

namespace App\Http\Requests;


class TrackingRequest extends JsonApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'sku_id' => 'required|numeric',
            'email' => 'required|string|email',
        ];
    }
}
