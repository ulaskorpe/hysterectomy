<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParasutCustomer extends Model
{
    use HasFactory;

    protected $fillable = ['parasut_id','name','email','phone','user_id'];

    protected static function booted()
    {        
        static::creating(function ($order) {
            if( auth()->check() ){
                $order->user_id = auth()->user()->id;
            }
        });
    }
}
