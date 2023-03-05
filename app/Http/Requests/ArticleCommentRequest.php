<?php


namespace App\Http\Requests;


class ArticleCommentRequest extends JsonApiRequest
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
            'comment' => 'required|string|min:5',
            'article_id' => 'required|numeric',
            'reply_id' => 'numeric',
        ];
    }
}
