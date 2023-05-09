<?php


namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            'name' => 'string|min:5',
            'sex' => 'in:0,1|nullable',
            'birthday_year' => 'nullable|numeric',
        ];
    }
}
