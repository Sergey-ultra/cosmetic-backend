<?php

namespace App\Repositories\SkuRepository\DTO;

final class SkuListOptionDTO
{
    public function __construct(
        public readonly ?string $search,
        public readonly ?array $brandIds,
        public readonly ?array $categoryIds,
        public readonly ?array $activeIngredientsGroupIds,
        public readonly ?array $countryIds,
        public readonly ?array $volumes,
        public readonly ?int $maxPrice,
        public readonly ?int $minPrice,
        public readonly ?string $sort,
        public readonly ?int $perPage,
        public readonly ?int $page,
    ){}
}
