<?php

declare(strict_types=1);

namespace App\Services\Parser;


class Logger
{
    protected const PATH = __DIR__ . '/../../../price-log.txt';

    public static function  write(string $text): void
    {
        $fileStream = fopen(self::PATH, 'a+');
        fwrite($fileStream, $text . "\r\n");
        fclose($fileStream);
    }
}
