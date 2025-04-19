<?php

namespace App\Repositories\SkuRepository\DTO;

final class SkuDTO
{
    public function __construct(
        public readonly int $categoryId,
        public readonly int $brandId,
        public readonly string $name,
        public readonly string $brandName,
        public readonly string $description,
        public readonly string $volume,
        public readonly array $images,
        public readonly string $status,
    ){}
}
