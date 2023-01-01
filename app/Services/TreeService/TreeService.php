<?php

declare(strict_types=1);

namespace App\Services\TreeService;

class TreeService implements TreeInterface
{
    public  function buildTree(array &$items, string $field, $initValue = null): array
    {
        $tree = [];

        foreach ($items as $key => $element) {
            if ($element[$field] == $initValue) {
                $children = $this->buildTree($items, $field, $element['id']);
                if (count($children)) {
                    $element['children'] = $children;
                }
                $tree[] = $element;
                unset($items[$key]);
            }
        }
        return $tree;
    }
}