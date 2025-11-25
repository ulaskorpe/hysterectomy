<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\HasRevisions;


class ProductPrice extends Model
{
    use LogsActivity, HasRevisions;

    protected $fillable = [
        'priceable_id',
        'currency_id',
        'priceable_type',
        'price',
        'final_price'
    ];

    protected $casts = [
        'price' => 'float',
        'final_price' => 'float'
    ];

    public function discount(){
        return $this->hasOne(ProductPriceDiscount::class);
    }

    public function priceable()
    {
        return $this->morphTo();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->logOnlyDirty();
    }
}
