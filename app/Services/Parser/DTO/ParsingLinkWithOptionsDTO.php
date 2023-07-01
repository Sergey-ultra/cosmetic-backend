<?php


namespace App\Services\Parser\DTO;


class ParsingLinkWithOptionsDTO
{
    public readonly int $id;
    public readonly string $link;
    public readonly int $store_id;
    public readonly int $category_id;
    public readonly ?string $body;
    public readonly bool $check_images_count;
    public readonly array $options;
    public readonly string $imgTag;
    public readonly string $imgAttr;
}
