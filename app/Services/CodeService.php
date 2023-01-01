<?php

declare(strict_types=1);

namespace App\Services;


use App\Models\Product;

class CodeService
{

//    public static function  getSkuByPathCode($productSkuCode)
//    {
//        $pieces = explode("-", $productSkuCode);
//        $productCode = $pieces[0];
//        $skuVolume = $pieces[1];
//
//        return $sku = Product::where('code', $productCode)->first()->skus->where('volume', $skuVolume)->first();
//    }


    public static function makeCode($string)
    {
        $string = preg_replace('~[Tt]he|~', '', $string);
        $string = mb_strtolower($string, 'UTF-8');



        //return $string = iconv("utf-8", "us-ascii//TRANSLIT", $string);
        $string = preg_replace('#\+\s+|\"|\-\s+#u', '', $string);
        $string = preg_replace('#\s+|\'|\`#u', ' ', $string);

        $string = trim(preg_replace('#\(\)|"#', '', $string));
        $string = trim(preg_replace('#,,#', '', $string));
        $string = trim(preg_replace('#/#', '', $string));
        $string = trim($string, '?');
        $string = trim($string, '.');
        $string = trim($string);

        return self::latin($string);
    }

    private static function latin($string)
    {
        $char = ['а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','з'=>'z','и'=>'i',
            'й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t',' '=>'-',
            'у'=>'u','ф'=>'f','х'=>'h',"'"=>'','ы'=>'i','э'=>'e','ж'=>'zh','ц'=>'ts','ч'=>'ch','ш'=>'sh',
            'щ'=>'j','ь'=>'','ю'=>'yu','я'=>'ya','Ж'=>'ZH','Ц'=>'TS','Ч'=>'CH','Ш'=>'SH','Щ'=>'J',
            'Ь'=>'','Ю'=>'YU','Я'=>'YA','ї'=>'i','Ї'=>'Yi','є'=>'ie','Є'=>'Ye','А'=>'A','Б'=>'B','В'=>'V',
            'Г'=>'G','Д'=>'D','Е'=>'E','Ё'=>'E','З'=>'Z','И'=>'I','Й'=>'Y','К'=>'K','Л'=>'L','М'=>'M','Н'=>'N',
            'О'=>'O','П'=>'P','Р'=>'R','С'=>'S','Т'=>'T','У'=>'U','Ф'=>'F','Х'=>'H','Ъ'=>"'",'Ы'=>'I','Э'=>'E'];
        return  strtr($string, $char);
    }
}