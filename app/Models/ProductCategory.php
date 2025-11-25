<?php

namespace App\Models;

use App\Traits\HasLink;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use App\Traits\HasLibraryMedia;
use App\Traits\HasSlugOriginal;
use Spatie\Sluggable\SlugOptions;
use Spatie\Activitylog\LogOptions;
use App\Models\Scopes\LanguageScope;
use App\Traits\HasClearCache;
use Spatie\EloquentSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\HasRevisions;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model implements Sortable
{
    use SoftDeletes, HasLibraryMedia, SortableTrait, HasSlug, HasLink, HasSlugOriginal, LogsActivity, HasRevisions, HasClearCache;

    protected $fillable = [
        'uuid',
        'language',
        'parent_id',
        'name',
        'summary',
        'description',
        'slug',
        'slug_org',
        'additional',
        'is_published',
        'start_date',
        'end_date',
        'order_column',
        'product_type_id',
        'content',
        'users_only',
        'pivot_data',
        'seo'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_published' => 'boolean',
        'additional' => 'array',
        'content' => 'array',
        'users_only' => 'boolean',
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

        static::addGlobalScope('isPublished', function (Builder $builder) {
            if (request()->route()->getPrefix() !== 'admin') {
                $builder->where('is_published',true);
            }
        });
    }

    public function connected_product_categories()
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

    public function product_type(){
        return $this->belongsTo(ProductType::class);
    }

    public function products(){

        return $this->belongsToMany(
            Product::class,
            'product_has_categories',
            'product_category_id',
            'product_id'
        );

    }

    public function parent()
    {
        return $this->belongsTo(self::class);
    }

    public function childs()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }


    //bununla model base tree yapiyoruz. ayrica tree de basina -- --- gibi ekleme.
    public static function nestable($contents,$level = 0)
    {

        foreach ($contents as $content) {

            $arrows = $level > 0 ? str_repeat('&#8614;',$level).' ' : '';
            $content->name = $arrows.$content->name;

            if (!$content->childs->isEmpty()) {
                $content->childs = self::nestable($content->childs,$level+1);
            }

        }

        return $contents;
    }

    
    public function createBreadCrumb(array $items_arr, $parent){

		$breadcrumb = $items_arr;
		
        if($parent){
            $item = array(
                'title' => $parent->name,
                'url' => '/'.$parent->uri->link
            );

            array_unshift($breadcrumb,$item);

            if( $parent->parent ){
                return $this->createBreadCrumb($breadcrumb,$parent->parent);
            }
        }

		return $breadcrumb;

	}
	public function getBreadCrumb(){
		
		return $this->createBreadCrumb(array(),$this->parent);

	}

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->logOnlyDirty();
    }
}
