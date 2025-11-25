<?php

namespace App\Models;

use App\Traits\HasTaxonomy;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use App\Traits\HasSlugOriginal;
use Spatie\Sluggable\SlugOptions;
use Spatie\Activitylog\LogOptions;
use App\Models\Scopes\LanguageScope;
use App\Traits\HasClearCache;
use Spatie\EloquentSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\HasRevisions;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductType extends Model implements Sortable
{
    use SoftDeletes, SortableTrait, HasSlug, HasTaxonomy, HasSlugOriginal, LogsActivity, HasRevisions, HasClearCache;

    protected $fillable = [
        'name',
        'summary',
        'slug',
        'slug_org',
        'taxable',
        'tax_rate',
        'additional',
        'stock_usage',
        'is_published',
        'start_date',
        'end_date',
        'order_column',
        'is_cartable',
        'is_contactable',
        'product_option_group_id',
        'after_order_type',
        'popup_products',
        'contact_form_id',
        'tax_id',
        'users_only',
        'language',
        'uuid',
        'pivot_data',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_published' => 'boolean',
        'taxable' => 'boolean',
        'stock_usage' => 'boolean',
        'additional' => 'array',
        'is_cartable' => 'boolean',
        'is_contactable' => 'boolean',
        'content' => 'array',
        'popup_products' => 'boolean',
        'users_only' => 'boolean',
        'pivot_data' => 'array',
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

    public function connected_product_types()
    {
        return $this->hasMany(self::class, 'uuid', 'uuid')->withoutGlobalScope(LanguageScope::class)->select('id','uuid','name','language');
    }

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function contact_form() {
        return $this->belongsTo(Form::class,'contact_form_id');
    }

    public function categories() {
        return $this->hasMany(ProductCategory::class);
    }

    public function products() {
        return $this->hasMany(Product::class);
    }
    
    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    public function option_group()
    {
        return $this->belongsTo(ProductOptionGroup::class,'product_option_group_id','id');

    }

    public function product_attributes()
    {
        return $this->belongsToMany(
            ProductAttribute::class,
            'product_type_has_attributes',
            'product_type_id',
            'product_attribute_id'
        )->with('values');

    }

    public function product_customer_attributes()
    {
        return $this->belongsToMany(
            ProductCustomerAttribute::class,
            'product_type_has_customer_attributes',
            'product_type_id',
            'product_customer_attribute_id'
        )->with('values');

    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->logOnlyDirty();
    }


}