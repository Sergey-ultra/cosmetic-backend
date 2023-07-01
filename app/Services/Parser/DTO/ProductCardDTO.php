<?php

declare(strict_types=1);


namespace App\Services\Parser\DTO;


class ProductCardDTO
{
    public  readonly string $link;
    public  readonly int $link_id;
    public  readonly int $category_id;
    public  readonly string $name;
    public  readonly ?string $name_en;
    public  readonly string $country;
    public  readonly string $brand;
    public  readonly string $volume;
    public  readonly array $images;
    public  readonly array $imageLinks;
    public  readonly int $price;
    public  readonly string $code;
    public  readonly string $description;
    public  readonly array $ingredient;
    public  readonly string $application;
    public  readonly string $purpose;
    public  readonly string $effect;
    public  readonly string $age;
    public  readonly string $type_of_skin;
}
