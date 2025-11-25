<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parasut extends Model
{
    protected $fillable = ['parasut_id', 'parasutable_type', 'parasutable_id','json_data'];

    protected $casts = [
        'json_data' => 'array'
    ];

    public function parasutable()
    {
        return $this->morphTo();
    }

}
