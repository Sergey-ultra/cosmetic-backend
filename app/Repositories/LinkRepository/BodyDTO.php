<?php

namespace App\Repositories\LinkRepository;

final class BodyDTO
{
    public function __construct(
        public readonly int $code,
        public readonly string $content
    ){}
}
