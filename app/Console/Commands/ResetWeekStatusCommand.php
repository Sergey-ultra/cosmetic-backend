<?php

namespace App\Console\Commands;


use App\Configuration;
use Illuminate\Console\Command;

class ResetWeekStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'week-status:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Сбросить недельный статус парсинга цен';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Configuration $configuration): int
    {
        $configuration->setWeekStatus(true);

        $this->info('Сброс недельного статуса парсинга цен произошел успешно');
        return 1;
    }
}
