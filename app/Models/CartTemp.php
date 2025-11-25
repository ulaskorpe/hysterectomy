<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartTemp extends Model
{
    use HasFactory;

    protected $fillable = ['session_id','cart_options','user_id'];

    protected $casts = [
        'cart_options' => 'array'
    ];

    //DB ye json lari tr problemi yasamasin.
    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
}
