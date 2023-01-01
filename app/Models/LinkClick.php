<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkClick extends Model
{
   protected $fillable = ['link_id', 'ip'];
}
