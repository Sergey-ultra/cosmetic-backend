<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\DropPriceMail;
use App\Models\Tracking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class PriceDropJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected array $skuWithDroppedPrice)
    {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            $skus = Tracking::select(
                'trackings.email as email',
                'products.name as name',
                'products.code as code',
                'skus.id as id',
                'skus.volume as volume',
                'skus.images as image'
            )
                ->leftJoin('skus','skus.id', '=','trackings.sku_id')
                ->join('products', 'products.id', '=', 'skus.product_id')
                ->whereIn('trackings.sku_id', $this->skuWithDroppedPrice)
                ->get();


            foreach($skus as &$sku) {
                $images = json_decode($sku['image'], true);
                $sku['image'] = $images[0]['image'];
            }


            $skus = $skus->groupBy('email')->all();



            foreach ($skus as $emailKey => $sku) {
                Mail::to($emailKey)->send(new DropPriceMail($sku));
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
