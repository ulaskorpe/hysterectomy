<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    protected $fillable = [
        'priceable_id',
        'priceable_type',
        'stock',
    ];

}
