<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model
{
    use HasFactory,SoftDeletes,LogsActivity;

    protected $fillable = [
        'type',
        'discount',
        'start_date',
        'end_date',
        'usage_count',
        'used_count',
        'apply_cart',
        'coupon_group_id',
        'code',
        'as_voucher',
        'used_amount'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'apply_cart' => 'boolean',
        'as_voucher' => 'boolean',
    ];

    public function group(){
        return $this->belongsTo(CouponGroup::class,'coupon_group_id','id');
    }

    public function product_types(){

        return $this->belongsToMany(
            ProductType::class,
            'coupon_product_types',
            'coupon_id',
            'product_type_id'
        );

    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->logOnlyDirty();
    }

}
