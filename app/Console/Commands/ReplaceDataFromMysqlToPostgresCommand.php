<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

class ReplaceDataFromMysqlToPostgresCommand extends Command
{
    private const MYSQL_DATABASE = 'sanctum';
    /**
     * @var string
     */
    protected $signature = 'data:replace';

    /**
     * @var string
     */
    protected $description = 'Переместить данные из MySql в Postgres';

    public function handle(): void
    {
        ini_set('memory_limit', '500M');

        $tables = DB::connection('mysql')->select("SELECT table_name FROM information_schema.tables
WHERE table_schema = 'sanctum';");

        $tables = (new Collection($tables))
            ->map(fn(stdClass $table) => $table->TABLE_NAME)
            ->filter(fn(string $table) => !in_array($table, ['parsing_links', 'migrations'], true))
            ->values()
            ->all();

        foreach ($tables as $table) {
            $this->handleOneTable($table);
        }
    }

    private function handleOneTable(string $table): void
    {
        gc_collect_cycles();

        $this->info(sprintf("Обработка таблицы %s", $table));

        $rows = DB::connection('mysql')
            ->table($table)
            ->get()
            ->map(fn (stdClass $item) => get_object_vars($item))
            ->all();

        if (count($rows) > 1500) {
            $rows = array_chunk($rows, 1500, true);
            echo 'Использование памяти ' . (memory_get_usage(true) / 1024 / 1024) . \PHP_EOL;
            foreach ($rows as $chunk) {
                DB::table($table)->insert($chunk);
            }
        } else {
            echo 'Использование памяти ' . (memory_get_usage(true) / 1024 / 1024) . \PHP_EOL;
            DB::table($table)->insert($rows);
        }
    }
}
