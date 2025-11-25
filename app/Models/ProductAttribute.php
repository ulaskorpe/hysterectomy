<?php

namespace App\Models;

use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use App\Traits\HasLibraryMedia;
use App\Traits\HasSlugOriginal;
use Spatie\Sluggable\SlugOptions;
use Spatie\Activitylog\LogOptions;
use App\Models\Scopes\LanguageScope;
use Spatie\EloquentSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\HasRevisions;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductAttribute extends Model implements Sortable
{
    use SoftDeletes, HasLibraryMedia, SortableTrait, HasSlug, HasSlugOriginal, LogsActivity, HasRevisions;

    protected $fillable = [
        'name',
        'slug',
        'slug_org',
        'option_type',
        'fe_visible',
        'is_required',
        'order_column',
        'language',
        'uuid',
        'pivot_data',
        'site_view',
        'unit_scale'
    ];

    protected $casts = [
        'fe_visible' => 'boolean',
        'is_required' => 'boolean',
        'pivot_data' => 'array',
        'unit_scale' => 'boolean',
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

        return $this->belongsToMany(
            ProductType::class,
            'product_type_has_attributes',
            'product_attribute_id',
            'product_type_id'
        )->withoutGlobalScope(LanguageScope::class);

    }


    public function values(){
        return $this->hasMany(ProductAttributeValue::class);
    }

    public function connected_product_attributes()
    {
        return $this->hasMany(self::class, 'uuid', 'uuid')->withoutGlobalScope(LanguageScope::class)->with(['values','product_types']);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*','values'])
        ->logOnlyDirty();
    }

}
