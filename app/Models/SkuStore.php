<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SkuStore extends Pivot
{
    public const TABLE = 'sku_store';

    protected $table = self::TABLE;

    protected $fillable = ['id', 'sku_id', 'store_id', 'link_id', 'price', 'created_at', 'updated_at'];


//    public static function boot()
//    {
//        parent::boot();
//
//        static::created(function ($price) {
//            //event(new AppointmentCreated($price));
//        });
//    }


    public function link(): BelongsTo
    {
        return $this->belongsTo(Link::class, 'link_id', 'id');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id', 'id');
    }
}
