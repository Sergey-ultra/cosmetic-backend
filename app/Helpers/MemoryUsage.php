<?php

namespace App\Helpers;

class MemoryUsage
{
    public static function getMemoryUsage(): string
    {
        $size = memory_get_usage(true);
        $unit = ['b','kb','mb','gb','tb','pb'];
        return round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }
}
