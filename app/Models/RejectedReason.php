<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RejectedReason extends Model
{
    public const TABLE = 'rejected_reasons';

    protected $table = self::TABLE;

    protected $fillable = ['reason'];

    public function reviews(): BelongsToMany
    {
        return $this->belongsToMany(Review::class, 'review_reason');
    }
}
