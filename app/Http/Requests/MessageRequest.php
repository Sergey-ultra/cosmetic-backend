<?php

namespace App\Http\Requests;

class MessageRequest extends JsonApiRequest
{
    public function rules(): array
    {
        return [
            'message' => 'required|string',
            'dialog_user_id' => 'nullable|integer',
        ];
    }
}
