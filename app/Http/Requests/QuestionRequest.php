<?php

declare(strict_types=1);


namespace App\Http\Requests;


class QuestionRequest extends JsonApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'body' => 'required|string|min:5'
        ];
    }
}
