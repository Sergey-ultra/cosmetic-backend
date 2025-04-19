<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use stdClass;

class ReplaceParsingLinksTableCommand extends Command
{
    private const TABLE = 'parsing_links';

    protected $signature = 'data:replace-parsing-links';

    /**
     * @var string
     */
    protected $description = 'Переместить данные таблицы parsing_links';

    public function handle(): void
    {
        $this->info("Обработка таблицы parsing_links");

        $rows = DB::connection('mysql')
            ->table(self::TABLE)
            ->get()
            ->map(fn (stdClass $item) => get_object_vars($item))
            ->all();

        if (count($rows) > 1500) {
            $rows = array_chunk($rows, 1500, true);
            echo 'Использование памяти ' . (memory_get_usage(true) / 1024 / 1024) . \PHP_EOL;
            foreach ($rows as $chunk) {
                DB::table(self::TABLE)->insert($chunk);
            }
        } else {
            echo 'Использование памяти ' . (memory_get_usage(true) / 1024 / 1024) . \PHP_EOL;
            DB::table(self::TABLE)->insert($rows);
        }
    }
}
