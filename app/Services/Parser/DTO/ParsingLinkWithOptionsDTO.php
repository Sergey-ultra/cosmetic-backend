<?php


namespace App\Services\Parser\DTO;


class ParsingLinkWithOptionsDTO
{
    public int $id;
    public string $link;
    public int $store_id;
    public int $category_id;
    public ?string $body;
    public bool $check_images_count;
    public array $options;
    public string $imgTag;
    public string $imgAttr;
}
