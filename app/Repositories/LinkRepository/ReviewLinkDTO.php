<?php

namespace App\Repositories\LinkRepository;

final class ReviewLinkDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $link,
        public readonly string $category_id,
        public readonly ?string $body,
    ){}
}
