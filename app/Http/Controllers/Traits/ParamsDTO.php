<?php

namespace App\Http\Controllers\Traits;

final class ParamsDTO
{
    public function __construct(
        public readonly array $filter,
        public readonly ?string $sort,
    ){}
}
