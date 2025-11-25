<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderProduct extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'count',
        'price',
        'discount', //sadece 1 adet icin olabilir. count birden fazla ise 1 tanesi icin uygulanmistir.
    ];

    protected $casts = [
        'price' => 'float',
        'discount' => 'float',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->withTrashed();
    }

    public function product_variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id')->withTrashed();
    }


    public function scopeStartDate(Builder $query, $date): Builder
    {
        $localDate = Carbon::parse($date)->setTimezone('Europe/Istanbul');
        return $query->where('created_at', '>=', $localDate);
    }

    public function scopeEndDate(Builder $query, $date): Builder
    {
        $localDate = Carbon::parse($date)->setTimezone('Europe/Istanbul');
        return $query->where('created_at', '<=', $localDate);
    }

}
