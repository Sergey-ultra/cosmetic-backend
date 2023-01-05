<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class SearchCategoriesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'url' => $this->category_url,
            'name' => $this->category_name,
        ];
    }
}
