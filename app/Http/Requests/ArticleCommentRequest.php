<?php


namespace App\Http\Requests;


class ArticleCommentRequest extends JsonApiRequest
{
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
