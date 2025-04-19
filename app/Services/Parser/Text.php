<?php

declare(strict_types=1);

namespace App\Services\Parser;


class Text
{
    public static function makeProductCode(string $brand, string $name): string
    {
        $brand = trim(preg_replace('~[Tt]he|~', '', $brand));
        $brand = mb_strtolower($brand, 'UTF-8');
        $name = mb_strtolower($name, 'UTF-8');

        if ($brand !== '' && strpos($name, $brand) === false) {
            $name =   $brand . '-' . $name;
        }


        //убрать запятые
        $name = preg_replace('#\,#u', '', $name);
        //убрать двоеточие
        $name = preg_replace('#:#u', '', $name);
        //убрать знак (.)
        $name = preg_replace('#\(\.\)#u', '', $name);
        //убрать слэш /
        $name = preg_replace('#\/#u', '', $name);


        //return $string = iconv("utf-8", "us-ascii//TRANSLIT", $string);

        $name = preg_replace('#\s+|\'|\`#u', ' ', $name);
        $name = preg_replace('#\+\s+|\"|\-\s+#u', '', $name);
        $name = preg_replace('#\-$#u', '', $name);
        return self::changeLangToEnglish($name);


        //$array[] = preg_replace('~[^-a-z0-9%_]+~', '-', $string);
        //$array[] = preg_replace('/[\x{0410}-\x{042F}]+.*[\x{0410}-\x{042F}]+/iu', ' ', $string);
        //return $array;
    }

    public static function makeCode(string $name): string
    {
        $name = mb_strtolower($name, 'UTF-8');
        $name = preg_replace('#\+\s+|\"|\-\s+#u', '', $name);
        $name = preg_replace('#\s+|\'|\`#u', ' ', $name);
        return self::changeLangToEnglish($name);
    }



    public static function makeIngredientCode(string $string): string
    {
        $string = str_replace(' (Water)', '', $string);
        return self::changeLangToEnglish($string);
    }

    public static function changeLangToEnglish(string $string): string
    {
        $string = mb_strtolower($string, 'UTF-8');
        return self::latin($string);
    }

    private static function latin(string $st): string
    {
        $char = ['а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','з'=>'z','и'=>'i',
            'й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t',
            ' '=>'-','+' => '','/' => '-',
            'у'=>'u','ф'=>'f','х'=>'h',"'"=>'','ы'=>'i','э'=>'e','ж'=>'zh','ц'=>'ts','ч'=>'ch','ш'=>'sh',
            'щ'=>'j','ь'=>'','ю'=>'yu','я'=>'ya','Ж'=>'ZH','Ц'=>'TS','Ч'=>'CH','Ш'=>'SH','Щ'=>'J',
            'Ь'=>'','Ю'=>'YU','Я'=>'YA','ї'=>'i','Ї'=>'Yi','є'=>'ie','Є'=>'Ye','А'=>'A','Б'=>'B','В'=>'V',
            'Г'=>'G','Д'=>'D','Е'=>'E','Ё'=>'E','З'=>'Z','И'=>'I','Й'=>'Y','К'=>'K','Л'=>'L','М'=>'M','Н'=>'N',
            'О'=>'O','П'=>'P','Р'=>'R','С'=>'S','Т'=>'T','У'=>'U','Ф'=>'F','Х'=>'H','Ъ'=>"'",'Ы'=>'I','Э'=>'E'];
        return  strtr($st, $char);
    }


    public static function random(int $length = 16): string
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }
}
