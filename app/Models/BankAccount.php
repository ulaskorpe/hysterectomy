<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\HasRevisions;

class BankAccount extends Model
{
    use SoftDeletes, LogsActivity, HasRevisions;

    protected $table = "bank_accounts";

    protected $fillable = [
        'bank',
        'holder',
        'iban',
        'currency'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->logOnlyDirty();
    }

}
