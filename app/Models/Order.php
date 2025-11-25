<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory,SoftDeletes, LogsActivity;

    protected $fillable = [
        'user_id',
        'cart_details',
        'order_status_id',
        'subtotal',
        'total',
        'coupon_id',
        'iyzico_token',
        'parasut_earsiv_id',
        'parasut_fatura_id',
        'code',
        'payment_type',
        'notes',
        'campaign_id',
    ];

    protected $casts = [
        'cart_details' => 'array',
        'product_types' => 'array',
        'subtotal' => 'float',
        'total' => 'float',
    ];

    //DB ye json lari tr problemi yasamasin.
    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
    
    protected static function booted()
    {        
        static::creating(function ($order) {
            if( auth()->check() ){
                $order->user_id = auth()->user()->id;
            }
        });
    }

    public function status(){
        return $this->belongsTo(OrderStatus::class,'order_status_id');
    }

    public function coupon(){
        return $this->belongsTo(Coupon::class,'coupon_id')->withTrashed()->with('group');
    }

    public function campaign(){
        return $this->belongsTo(Campaign::class,'campaign_id')->withTrashed();
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id')->withTrashed();
    }

    public function products(){
        return $this->hasMany(OrderProduct::class);
    }

    public function scopeStartDate(Builder $query, $date): Builder {
        $localDate = Carbon::parse($date)->setTimezone('Europe/Istanbul');
        return $query->where('created_at', '>=', $localDate);
    }

    public function scopeEndDate(Builder $query, $date): Builder {
        $localDate = Carbon::parse($date)->setTimezone('Europe/Istanbul');
        return $query->where('created_at', '<=', $localDate);
    }


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->logOnlyDirty();
    }
}
