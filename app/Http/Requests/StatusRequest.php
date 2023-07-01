<?php

declare(strict_types=1);


namespace App\Http\Requests;


class StatusRequest extends JsonApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'status' => 'required|string|in:rejected,deleted,moderated,published',
        ];
    }
}
