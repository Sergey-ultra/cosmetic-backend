<?php


namespace App\Services\SkuService;


use App\Models\Product;

interface SkuInterface
{
    public function setAllSkus(array $array): void;

    public function setSmallImagesFolder(string $folder): void;

    public function groupAllSkusWithPricesToOneSku(): self;

    public function groupSkusToOneProduct(): self;

    public function sort(string $sort): self;

    public function paginate(int $page, int $perPage);

    public function createNewSku(array $params): Product;
}
