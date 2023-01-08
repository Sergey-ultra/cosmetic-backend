<?php


namespace App\Http\Resources\Admin;


use Illuminate\Http\Resources\Json\ResourceCollection;

class SkuVideoCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->transform(function ($item) {
                return new SkuVideoResource($item);
            }),
        ];
    }
}
