<?php

declare(strict_types=1);

namespace App\Services\Parser;


use App\Services\Parser\DTO\ExistingProductDTO;

class Utils
{
    public static function splitProductName (string $name): string
    {
        $productPattern = '#\d+%|[\+\-,\/]#iu';
        $productName =  preg_replace($productPattern, ' ', $name);
        if ($productName === '' ) {
            $productName = $name;
        }
        return '%' . preg_replace('#\s+|\-#', '%', trim($productName)) . '%';
    }



    public static function findExisting(array $items, array $nameValues, string $explodeChar): array
    {
        $idsAndName = [];
        foreach ($items as  $item) {
            $currentName = '';

            foreach ($nameValues as $key => $name) {
                $name = trim($name, $explodeChar);
                $nameParts = explode($explodeChar, mb_strtolower($name, 'UTF-8'));
                $cond = true;
                foreach ($nameParts as $namePart) {
                    if (strpos(mb_strtolower($item["name"], 'UTF-8'), $namePart) === false) {
                        $cond = false;
                        break;
                    }
                }

                if ($cond) {
                    $currentName = $name;
                    unset($nameValues[$key]);
                    break;
                }
            }

            if ($currentName) {
                $idsAndName[$currentName] = $item['id'];
            }
        }

        return $idsAndName;
    }



    public static function findExistingProducts(array $existingProducts, array $nameValues, string $explodeChar): array
    {
        $idsAndName = [];
        foreach ($existingProducts as $product) {
            $currentName = '';

            foreach ($nameValues as $key => $name) {
                $nameWithoutFirstAndLastSymbol = trim($name, $explodeChar);
                $nameParts = explode($explodeChar, mb_strtolower($nameWithoutFirstAndLastSymbol, 'UTF-8'));
                $isMatch = true;
                foreach ($nameParts as $namePart) {
                    if (strpos(mb_strtolower($product["name"], 'UTF-8'), $namePart) === false) {
                        $isMatch = false;
                        break;
                    }
                }

                if ($isMatch) {
                    $currentName = $name;
                    unset($nameValues[$key]);
                    break;
                }
            }

            if ($currentName) {
                $idsAndName[$currentName][] = new ExistingProductDTO(
                    $product['id'],
                    $product['brand_id'],
                    count($product['ingredient_ids'])
                );
            }
        }

        return $idsAndName;
    }


    public static function makeEnglishProductName(string $string, string $brandName = ""): string
    {
        $string = preg_replace("#$brandName#", '', $string );
        $string = preg_replace('#[^a-zA-Z0-9\'\s+%.-]#', '', $string);
        $string = preg_replace('#%#', ' ', $string );
        $string = preg_replace('#\s+#', ' ', $string );
        $string = trim(preg_replace('#\s+-#', '', $string));
        return  trim(trim($string, '.'));
    }

    public static function knownLanguage(string $string): string
    {
        preg_match_all( '/[а-яё]/ui', $string, $rusMatches);
        if (count($rusMatches[0]) / mb_strlen($string) > 0.5) {
            return 'rus';
        }
        return 'en';
    }
}
