<?php

namespace App\Http\Requests;


class UserUpdateRequest extends JsonApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'string|min:5',
            'sex' => 'in:0,1|nullable',
            'birthday_year' => 'nullable|numeric',
        ];
    }
}
