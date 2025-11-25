<?php

namespace App\Models;

use App\Traits\HasLink;
use App\Traits\HasTags;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use App\Traits\HasLibraryMedia;
use App\Traits\HasSlugOriginal;
use Jackiedo\Cart\Facades\Cart;
use Spatie\Searchable\Searchable;
use Spatie\Sluggable\SlugOptions;
use Spatie\Activitylog\LogOptions;
use Spatie\Searchable\SearchResult;
use App\Models\Scopes\LanguageScope;
use App\Traits\HasClearCache;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\HasRevisions;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Campaign extends Model implements Sortable, Searchable
{
    use HasFactory, SoftDeletes, LogsActivity, HasRevisions, HasLibraryMedia, SortableTrait, HasSlug, HasSlugOriginal,HasLink, HasTags, HasClearCache;

    protected $table = "campaigns";

    protected $fillable = [
        'language',
        'name',
        'summary',
        'slug',
        'additional',
        'start_date',
        'end_date',
        'order_column',
        'content',
        'users_only',
        'apply_cart',
        'min_cart_amount',
        'type',
        'discount',
        'user_usage_count',
        'start_date',
        'end_date',
        'is_published',
        'uuid',
        'pivot_data',
        'seo'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'additional' => 'array',
        'content' => 'array',
        'users_only' => 'boolean',
        'is_published' => 'boolean',
        'apply_cart' => 'boolean',
        'pivot_data' => 'array',
        'seo' => 'array',
    ];

    protected static function booted()
    {

        static::creating(function ($c) {
            if(!$c->uuid) {
                $c->uuid = Str::uuid();
            }
        });

        static::addGlobalScope(new LanguageScope);
        
        static::addGlobalScope('publishDate', function (Builder $builder) {
            if (request()->route()->getPrefix() !== 'admin') {
                $builder->where(function ($qq) {
                    $qq->whereNull('start_date')
                    ->orWhereDate('start_date', '1970-01-01')
                    ->orWhereDate('start_date', '<=', now());
                });
            }
        });

        static::addGlobalScope('endDate', function (Builder $builder) {
            if (request()->route()->getPrefix() !== 'admin') {
                $builder->where(function ($qq) {
                    $qq->whereNull('end_date')
                    ->orWhereDate('end_date', '1970-01-01')
                    ->orWhereDate('end_date', '>=', now());
                });
            }
        });

        static::addGlobalScope('isPublished', function (Builder $builder) {
            if (request()->route()->getPrefix() !== 'admin') {
                $builder->where('is_published',true);
            }
        });

    }

    public function connected_campaigns()
    {
        return $this->hasMany(self::class, 'uuid', 'uuid')->withoutGlobalScope(LanguageScope::class)->select('id','uuid','name','language')->with('uri:linkable_id,final_uri');
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function product_types()
    {

        return $this->belongsToMany(
            ProductType::class,
            'campaign_product_types',
            'campaign_id',
            'product_type_id'
        );
    }

    public function createBreadCrumb(array $items_arr, $parent)
    {

        $breadcrumb = $items_arr;

        if ($parent) {
            $item = array(
                'title' => $parent->name,
                'url' => '/' . $parent->uri->link
            );

            array_unshift($breadcrumb, $item);

            if ($parent->parent) {
                return $this->createBreadCrumb($breadcrumb, $parent->parent);
            }
        }

        return $breadcrumb;
    }

    public function getBreadCrumb()
    {

        return $this->createBreadCrumb(array(), $this->parent);
    }

    public function getSearchResult(): SearchResult
    {
        return new \Spatie\Searchable\SearchResult(
            $this,
            $this->name,
            env('APP_URL') . '/' . $this->uri->link
        );
    }

    public function used_users(){

        return $this->belongsToMany(
            User::class,
            'user_used_campaigns',
            'campaign_id',
            'user_id'
        );

    }

    public function isUsable(){

        //uyelere ozelse
        if( $this->users_only ){
            
            if( !Auth::check() ){
                return false;
            }

            //uye kampanyadan yararlanmis mi?
            $isUsedByUser = $this->used_users->where('id',Auth::id())->first();

            if($isUsedByUser){
                return false;
            }

        }

        //sepet tutari
        if(Cart::getTotal() < $this->min_cart_amount){
            return false;
        }

        //tum sepette gecerli degilse
        if(!$this->apply_cart){
            
            $productTypeCount = 0;

            $cartItems = Cart::getDetails()->get('items');

            foreach ($cartItems as $key => $item) {
                
                $checkItem = $this->product_types->where('id', $item->extra_info['product_type_id'])->first();

                if( $checkItem ){
                    $productTypeCount++;
                }

            }

            if( $productTypeCount == 0 ){
                return false;
            }

        }

        return true;


    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->logOnlyDirty();
    }

}
