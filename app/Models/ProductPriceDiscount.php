<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\HasRevisions;

class ProductPriceDiscount extends Model
{
    use LogsActivity, HasRevisions;

    protected $fillable = [
        'product_price_id',
        'user_group_id',
        'discount_type',
        'value',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'value' => 'float',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];


    protected static function booted()
    {    
        //Simdilik tarihleri delan gozardi ediyorum
        static::creating(function ($discount) {
            $discount->start_date = now();
            $discount->end_date = now();
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->logOnlyDirty();
    }

}
