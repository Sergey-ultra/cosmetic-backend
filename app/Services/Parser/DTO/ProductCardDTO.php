<?php

declare(strict_types=1);


namespace App\Services\Parser\DTO;


class ProductCardDTO
{
    public  string $link;
    public  int $link_id;
    public  int $category_id;
    public  string $name;
    public  ?string $name_en;
    public  string $country;
    public  string $brand;
    public  string $volume;
    public  array $images = [];
    public  array $imageLinks = [];
    public  int $price;
    public  string $code;
    public  string $description;
    public  array $ingredient;
    public  string $application;
    public  string $purpose;
    public  string $effect;
    public  string $age;
    public  string $type_of_skin;
}