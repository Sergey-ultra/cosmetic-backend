<?php

namespace App\Repositories\LinkRepository;

interface LinkRepositoryInterface
{
    public function insertBody(int $linkOptionId, int $pageNumber, BodyDTO $body): void;
}
