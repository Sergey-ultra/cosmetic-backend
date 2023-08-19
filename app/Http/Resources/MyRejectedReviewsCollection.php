<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MyRejectedReviewsCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => $this->collection->transform(function ($item) {
                return new MyRejectedReviewsResource($item);
            })
        ];
    }
}
