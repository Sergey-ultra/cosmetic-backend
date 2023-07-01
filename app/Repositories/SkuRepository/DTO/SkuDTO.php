<?php

namespace App\Repositories\SkuRepository\DTO;

final class SkuDTO
{
    public function __construct(
        public readonly int $category_id,
        public readonly int $brand_id,
        public readonly string $name,
        public readonly string $brandName,
        public readonly string $description,
        public readonly string $volume,
        public readonly array $images,
    ){}
}
