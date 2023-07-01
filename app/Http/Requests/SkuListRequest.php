<?php

namespace App\Http\Requests;

use App\Repositories\SkuRepository\DTO\SkuListOptionDTO;

class SkuListRequest extends JsonApiRequest
{
    public function rules(): array
    {
        return [
            'search' => 'string',
            'category_code' => 'string',
            'brand_code' => 'string',
            'brand_ids' => 'array',
            'category_ids' => 'array',
            'active_ingredients_group_ids' => 'array',
            'country_ids' => 'array',
            'volumes' => 'array',
            'max_price' => 'numeric',
            'min_price' => 'numeric',
            'sort' => 'string',
            'per_page' => 'numeric',
            'page' => 'numeric',
        ];
    }


    public function getDto(): SkuListOptionDTO
    {
        return new SkuListOptionDTO(
            $this->input('search'),
            $this->input('brand_ids'),
            $this->input('category_ids'),
            $this->input('active_ingredients_group_ids'),
            $this->input('country_ids'),
            $this->input('volumes'),
            $this->input('max_price'),
            $this->input('min_price'),
            $this->input('sort'),
            $this->input('per_page'),
            $this->input('page'),
        );
    }
}
