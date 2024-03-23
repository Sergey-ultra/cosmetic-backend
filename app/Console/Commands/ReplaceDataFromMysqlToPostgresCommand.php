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
        ini_set('memory_limit', '2G');

        $tables = DB::connection('mysql')->select("SELECT table_name FROM information_schema.tables
WHERE table_schema = 'sanctum';");

        $tables = (new Collection($tables))
            ->map(fn(stdClass $table) => $table->TABLE_NAME)
            ->filter(fn(string $table) => $table !== 'migrations')
            ->values()
            ->all();

        foreach ($tables as $table) {
            $this->handleOneTable($table);
        }
    }

    private function handleOneTable(string $table): void
    {
        $rows = DB::connection('mysql')
            ->table($table)
            ->get()
            ->map(fn (stdClass $item) => get_object_vars($item))
            ->all();

        if (count($rows) > 5000) {
            $rows = array_chunk($rows, 5000, true);
            foreach ($rows as $chunk) {
                DB::table($table)->insert($chunk);
            }
        } else {
            DB::table($table)->insert($rows);
        }
    }
}
