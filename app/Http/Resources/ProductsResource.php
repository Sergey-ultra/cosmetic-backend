<?php

namespace App\Http\Resources;

use Dotenv\Util\Str;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductsResource extends JsonResource
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
           'id'=> $this->id,
           'name' => $this->name,
           'code' => $this->code,
           'description' => $this->description
       ];
    }
}
