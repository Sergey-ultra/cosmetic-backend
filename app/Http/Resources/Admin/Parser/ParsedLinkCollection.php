<?php

namespace App\Http\Resources\Admin\Parser;

use App\Http\Resources\Admin\ArticleResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ParsedLinkCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->transform(fn($link) => new ParsedLinkResource($link)),
        ];
    }
}
