<?php

declare(strict_types=1);


namespace App\Http\Requests;


class StatusRequest extends JsonApiRequest
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
            'status' => 'required|in:rejected,deleted,moderated,published',
        ];
    }
}
