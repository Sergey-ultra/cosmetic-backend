<?php
declare(strict_types=1);


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Visit extends Model {

//    public $attributes = ['hits' => 0];

    protected $fillable = ['ip', 'utm', 'hits'];

//    public $timestamps = false;

    protected $table = 'visit_statistic';

//    public static function boot() {
//        // When a new instance of this model is created...
//        parent::boot();
//        static::creating(function ($tracker) {
//            $tracker->hits = 0;
//        } );
//
//        // Any time the instance is saved (create OR update)
//        static::saving(function ($tracker) {
////            $tracker->visit_date = date('Y-m-d');
////            $tracker->visit_time = date('H:i:s');
//            $tracker->hits++;
//        } );
//    }


    public static function hit(Request $request) {
        $one = static::where(['ip' => $_SERVER['REMOTE_ADDR']])->first();
        if (!$one) {
            static::create([
                'ip' => $_SERVER['REMOTE_ADDR'],
                'utm' => $request->utm_source ?? NULL,
                'hits' => 1
            ]);
        } else {
            $one->hits++;
            $one->save();
        }
    }


}
