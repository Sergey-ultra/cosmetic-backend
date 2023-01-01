<?php

declare(strict_types=1);


namespace App\Services\TreeService;


interface TreeInterface
{
    public  function buildTree(array &$items, string $field, $initField = null): array;
}