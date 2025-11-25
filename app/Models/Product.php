<?php

namespace App\Models;

use App\Traits\HasLink;
use App\Traits\HasTags;
use App\Traits\HasParasut;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Illuminate\Support\Carbon;
use App\Traits\HasLibraryMedia;
use App\Traits\HasSlugOriginal;
use Jackiedo\Cart\Facades\Cart;
use Spatie\Searchable\Searchable;
use Spatie\Sluggable\SlugOptions;
use Spatie\Activitylog\LogOptions;
use Spatie\Searchable\SearchResult;
use App\Models\Scopes\LanguageScope;
use App\Traits\HasClearCache;
use Spatie\EloquentSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\HasRevisions;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model implements Sortable, Searchable
{
    use SoftDeletes, LogsActivity, HasRevisions, HasLibraryMedia, SortableTrait, HasSlug, HasSlugOriginal, HasLink, HasParasut, HasTags, HasClearCache;

    protected $fillable = [
        'uuid',
        'sku',
        'product_type_id',
        'language',
        'name',
        'summary',
        'description',
        'slug',
        'uri',
        'attributes',
        'additional',
        'is_published',
        'start_date',
        'end_date',
        'order_column',
        'stock',
        'after_order_download_name',
        'after_order_download_file',
        'after_order_email_subject',
        'after_order_email_body',
        'created_at',
        'use_option_group',
        'use_variables',
        'featured',
        'has_discount',
        'product_layout_id',
        'content',
        'tax_id',
        'order_count',
        'users_only',
        'pivot_data',
        'seo'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_published' => 'boolean',
        'additional' => 'array',
        'attributes' => 'array',
        'use_option_group' => 'boolean',
        'use_variables' => 'boolean',
        'featured' => 'boolean',
        'has_discount' => 'boolean',
        'content' => 'array',
        'users_only' => 'boolean',
        'pivot_data' => 'array',
        'seo' => 'array',
    ];

    //DB ye json lari tr problemi yasamasin.
    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
    
    protected static function booted()
    {
        static::creating(function ($c) {
            if(!$c->uuid) {
                $c->uuid = Str::uuid();
            }
        });

        static::addGlobalScope(new LanguageScope);

        static::addGlobalScope('isPublished', function (Builder $builder) {
            if (!in_array(request()->route()->getPrefix(),['/admin','admin'])) {
                $builder->where('is_published',true);
            }
        });

        //product_type olanlari alacagiz. urunun product_type silinmis olabilir. ama urun kalir.
        static::addGlobalScope('hasProductType', function (Builder $builder) {
            if (!in_array(request()->route()->getPrefix(),['/admin','admin'])) {
                $builder->whereHas('product_type');
            }
        });
        
    }

    protected $appends = ['incart'];

    public function connected_products()
    {
        return $this->hasMany(self::class, 'uuid', 'uuid')->withoutGlobalScope(LanguageScope::class)->select('id','uuid','name','language');
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function getSearchResult(): SearchResult
    {
        return new \Spatie\Searchable\SearchResult(
            $this,
            $this->name,
            env('APP_URL').'/'.$this->uri->link
        );
    }


    public function product_categories()
    {

        return $this->belongsToMany(
            ProductCategory::class,
            'product_has_categories',
            'product_id',
            'product_category_id'
        );
    }

    public function product_price()
    {
        return $this->morphOne(ProductPrice::class, 'priceable')->with('discount');
    }

    public function product_type()
    {
        return $this->belongsTo(ProductType::class)->withoutGlobalScope(LanguageScope::class)->with('option_group')->where('is_published',true);
    }

    public function product_variants()
    {
        return $this->hasMany(ProductVariant::class)->ordered()->with('price');
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    public function getIncartAttribute()
    {
        $result = [
            'in_cart' => false,
            'quantity' => 0,
            'hash' => null,
            'active_stock' => null
        ];

        if (in_array(request()->route()->getPrefix(),['/admin','admin'])) {
            return $result;
        }

        $cartItems = Cart::getDetails()->get('items');

        if ($cartItems->count() > 0) {
            $checkItem = $cartItems->where('id', $this->id)->first();

            if ($checkItem) {
                $result = [
                    'in_cart' => true,
                    'quantity' => $checkItem->quantity,
                    'hash' => $checkItem->hash,
                    'active_stock' => $this->product_type->stock_usage ? $this->stock - $checkItem->quantity : null
                ];
            }
        }
        return $result;
    }


    public function relatedProducts($count = 8)
    {

        $related = Product::where('id', '!=', $this->id)
            ->whereHas('product_categories', function ($query) {
                $query->whereIn('id', $this->product_categories()->pluck('id'));
            })->take($count)->get();

        return $related;
    }


    public function scopeStartDate(Builder $query, $date): Builder {
        $localDate = Carbon::parse($date)->setTimezone('Europe/Istanbul');
        return $query->where('created_at', '>=', $localDate);
    }

    public function scopeEndDate(Builder $query, $date): Builder {
        $localDate = Carbon::parse($date)->setTimezone('Europe/Istanbul');
        return $query->where('created_at', '<=', $localDate);
    }


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*','product_variants','product_categories','product_price']);
    }


}
