<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\HasRevisions;

class Cargo extends Model
{
    use SoftDeletes, LogsActivity, HasRevisions;

    protected $table = "cargos";

    protected $fillable = [
        'name',
        'fixed',
        'fixed_price',
        'free_after',
        'default_product_desi'
    ];

    protected $casts = [
        'fixed' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->logOnlyDirty();
    }

}
