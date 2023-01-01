<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticleWithTagsCollection extends ResourceCollection
{
    protected array $additionalFields;

    public function __construct($resource, array $additionalFields = [])
    {
        $this->additionalFields = $additionalFields;

        parent::__construct($resource);

        $this->resource = $this->collectResource($resource);
    }
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $outArray = [
            'data' => $this->collection->transform(function ($article) {
                return new ArticleWithTagsResource($article);
            }),
        ];

        if (count($this->additionalFields)) {
            return array_merge($outArray, $this->additionalFields);
        }
        return $outArray;
    }
}
