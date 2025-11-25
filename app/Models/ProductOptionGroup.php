<?php

namespace App\Models;

use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use App\Traits\HasLibraryMedia;
use App\Traits\HasSlugOriginal;
use Spatie\Sluggable\SlugOptions;
use Spatie\Activitylog\LogOptions;
use App\Models\Scopes\LanguageScope;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\HasRevisions;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductOptionGroup extends Model
{
    use SoftDeletes, HasLibraryMedia, HasSlug, HasSlugOriginal, LogsActivity, HasRevisions;

    protected $fillable = [
        'name',
        'slug',
        'slug_org',
        'has_own_price',
        'stock_usage',
        'variant_select_type',
        'language',
        'uuid',
    ];

    protected $casts = [
        'has_own_price' => 'boolean',
        'stock_usage' => 'boolean',
    ];

    protected static function booted()
    {        
        static::creating(function ($c) {
            if(!$c->uuid) {
                $c->uuid = Str::uuid();
            }
        });

        static::addGlobalScope(new LanguageScope);

    }

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }


    public function product_types(){

        return $this->hasMany(ProductType::class)->withoutGlobalScope(LanguageScope::class);

    }

    public function options(){
        return $this->hasMany(ProductOptionGroupOption::class)->ordered()->with('option_values');
    }

    public function connected_product_option_groups()
    {
        return $this->hasMany(self::class, 'uuid', 'uuid')->withoutGlobalScope(LanguageScope::class)->with('options');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->logOnlyDirty();
    }
    

}
