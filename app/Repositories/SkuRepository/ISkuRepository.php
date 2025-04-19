<?php


namespace App\Repositories\SkuRepository;


use App\Repositories\SkuRepository\DTO\SkuDTO;
use App\Repositories\SkuRepository\DTO\SkuListOptionDTO;
use Illuminate\Pagination\LengthAwarePaginator;

interface ISkuRepository
{
    public function setMode(string $mode, int $entityId): self;
    public function getList(SkuListOptionDTO $skuListOptionDto): LengthAwarePaginator;
    public function setSmallImagesFolder(string $folder): void;
    public function createNewSku(SkuDTO $sku): array;
    public function popularTenSkus(): array;
}
