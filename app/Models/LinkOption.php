<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
/** @property LinkPage[] $pages */
class LinkOption extends Model
{
    public const TABLE = 'link_options';
    protected $table = self::TABLE;

    protected $fillable = ['store_id', 'category_id', 'options'];

    protected $casts = [
        'options' => 'array',
    ];

    public function pages(): HasMany
    {
        return $this->hasMany(LinkPage::class,  'link_option_id', 'id');
    }
}
