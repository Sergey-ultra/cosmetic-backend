<?php


namespace App\Http\Requests;

class ParsingLinkIdsRequest extends JsonApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'ids' => 'required|array',
        ];
    }
}
