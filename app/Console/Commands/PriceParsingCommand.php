<?php

namespace App\Console\Commands;

use App\Services\Parser\PriceParsingByCronService;
use Illuminate\Console\Command;

class PriceParsingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'price:parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Парсинг цен по крону';

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
    public function handle(PriceParsingByCronService $priceParsingByCronService): int
    {
        $result = $priceParsingByCronService->handle();

        $this->info($result);
        $this->info('Парсинг цен по крону произошел успешно');
        return 1;
    }
}
