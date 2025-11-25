<?php

namespace App\Traits;

use App\Models\Parasut;

trait HasParasut
{

    protected static function bootHasParasut()
    {
        static::created(function ($content) {

            //

        });

        static::deleting(function ($content) {
            //
        });
    }

    public function parasut()
    {
        return $this->morphOne(Parasut::class, 'parasutable');
    }


}
