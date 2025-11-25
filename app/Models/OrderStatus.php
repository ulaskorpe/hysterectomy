<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderStatus extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'for_new',
        'for_wait_payment',
    ];

    protected $casts = [
        'for_new' => 'boolean',
        'for_wait_payment' => 'boolean',
    ];

    public function orders(){
        return $this->hasMany(Order::class,'order_status_id');
    }

}
