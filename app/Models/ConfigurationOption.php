<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ConfigurationOption extends Model
{
    protected $table = 'configuration_options';

    protected $fillable = ['key', 'value', 'description'];

    public static function boot()
    {
        parent::boot();

        static::created(function(self $item) {
            $options = Cache::get('options', []);
            $options[$item->key] = $item->value;
            Cache::put('options', $options, now()->addDay());
        });

        static::updated(function(self $item) {
            $options = Cache::get('options', []);
            $options[$item->key] = $item->value;
            Cache::put('options', $options, now()->addDay());
        });

        static::deleted(function(self $item) {
            $options = Cache::get('options');
            unset($options[$item->key]);
            Cache::put('options', $options, now()->addDay());
        });
    }
}

