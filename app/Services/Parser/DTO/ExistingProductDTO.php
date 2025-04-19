<?php

namespace App\Services\Parser\DTO;

class ExistingProductDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $brandId,
        public readonly int $ingredientsCount
    )
    {}
}
