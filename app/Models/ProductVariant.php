<?php

namespace App\Models;

use App\Traits\HasParasut;
use App\Traits\HasLibraryMedia;
use Jackiedo\Cart\Facades\Cart;
use Spatie\Activitylog\LogOptions;
use Spatie\EloquentSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\HasRevisions;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model implements Sortable
{
    
    use SoftDeletes, SortableTrait, HasParasut, HasLibraryMedia, LogsActivity, HasRevisions;

    protected $fillable = [
        'product_id',
        'product_option_group_id',
        'option_values',
        'additional',
        'order_column',
        'stock',
        'has_discount',
    ];

    protected $casts = [
        'option_values' => 'array',
        'additional' => 'array',
        'has_discount' => 'boolean',
    ];

    //DB ye json lari tr problemi yasamasin.
    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    protected $appends = ['incart'];

    public function price(){
        return $this->morphOne(ProductPrice::class, 'priceable')->with('discount');
    }

    public function product(){
        return $this->belongsTo(Product::class,'product_id','id');
    }

    public function option_group(){
        return $this->belongsTo(ProductOptionGroup::class,'product_option_group_id','id');
    }

    public function getIncartAttribute()
    {
        $result = [
            'in_cart' => false,
            'quantity' => 0,
            'hash' => null,
            'active_stock' => null
        ];

        $cartItems = Cart::getDetails()->get('items');

        if ($cartItems->count() > 0) {
            $checkItem = $cartItems->where('id', $this->id)->first();

            if ($checkItem) {
                $result = [
                    'in_cart' => true,
                    'quantity' => $checkItem->quantity,
                    'hash' => $checkItem->hash,
                    'active_stock' => $this->option_group->stock_usage ? $this->stock - $checkItem->quantity : null
                ];
            }
        }
        return $result;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->logOnlyDirty();
    }
}
