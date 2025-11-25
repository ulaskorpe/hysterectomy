<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CouponGroup extends Model
{
    use HasFactory,SoftDeletes, LogsActivity;

    protected $fillable = ['name','is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function coupons() {
        return $this->hasMany(Coupon::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->logOnlyDirty();
    }
    
}
