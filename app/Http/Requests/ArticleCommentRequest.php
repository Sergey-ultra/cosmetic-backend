<?php


namespace App\Http\Requests;


class ArticleCommentRequest extends MiddleRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'comment' => 'required|string|min:5',
            'article_id' => 'required|numeric',
            'reply_id' => 'numeric',
        ];
    }
}
