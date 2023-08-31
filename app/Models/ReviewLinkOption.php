<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReviewLinkOption extends Model
{
    public const TABLE = 'review_link_options';
    protected $table = self::TABLE;

    protected $fillable = ['category_id', 'options'];

    protected $casts = [
        'options' => 'array',
    ];

    public function pages(): HasMany
    {
        return $this->hasMany(ReviewLinkPage::class,  'review_link_option_id', 'id');
    }
}
