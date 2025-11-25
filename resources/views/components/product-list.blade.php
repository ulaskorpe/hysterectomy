@php

    //Ortak component. $segment, $productType veya $category gonderildiyse ona gore.
    //Ayrica category felan filtrelerde de olabilir. o ayri kafa karismasin

    $allowedFilters = ['featured'];


    $product_attributes = \App\Models\ProductAttribute::with('values')
    ->when($productType,function($query) use ($productType) {
        $query->whereHas('product_types',function($ptype) use ($productType) {
            $ptype->where('id',$productType->id);
        });
    })
    ->get();


    foreach ($product_attributes as $a => $attribute) {
        if( $attribute->option_type == 'select' || $attribute->option_type == 'multiselect'){

            $inputName = Str::slug($attribute->name,'_');
            $allowedFilters[] = \Spatie\QueryBuilder\AllowedFilter::callback($inputName, function ($query, $value) use($attribute) {

                
                $query->where(function($json) use($attribute, $value) {
                    foreach ($value as $att_key => $val) {
                        $json->orWhereJsonContains('attributes', [['value' => (int)$val, 'id' => $attribute->id]]);
                    }
                });
            });

        }
    }

    if( request()->input('filter') ){

        $filterParams = request()->input('filter');

        if( isset($filterParams['product_types']) ){

            $allowedFilters[] = \Spatie\QueryBuilder\AllowedFilter::callback('product_types', function ($query, $value) {
                $query->whereIn('product_type_id', $value);
            });

        }

        if( isset($filterParams['categories']) ){

            $allowedFilters[] = \Spatie\QueryBuilder\AllowedFilter::callback('categories', function ($query, $value) {
                $query->whereHas('product_categories', function ($q) use ($value) {
                    $q->whereIn('id', $value);
                });
            });

        }

        if( isset($filterParams['price']) ){

            $allowedFilters[] = \Spatie\QueryBuilder\AllowedFilter::callback('price', function ($query, $value) {
                $query->where(function($p) use ($value){
                    $p->whereHas('product_price', function ($q) use ($value) {
                        $q->whereBetween('final_price', $value);
                    })->orWhereHas('product_variants', function ($var) use ($value) {
                        $var->whereHas('price', function ($varp) use ($value) {
                            $varp->whereBetween('final_price', $value);
                        });
                    });
                });
            });

        }

    }

    $products = \Spatie\QueryBuilder\QueryBuilder::for(\App\Models\Product::class)
    ->with([
        'product_type',
        'main_image',
        'product_variants',
        'product_variants.main_image',
        'product_price',
        'product_categories',
        'tags',
        'uri'
    ])
    ->when(request()->input('search'), function ($query, $search) {
        $query->where(function (Builder $q) use ($search) {
            $q->where('name','like','%'.$search.'%')->orWhere('sku','like','%'.$search.'%');
        });
    })
    ->when($productType,function($query) use ($productType) {
        $query->where('product_type_id',$productType->id);
    })
    ->when($category,function($query) use ($category) {
        $query->whereHas('product_categories', function ($q) use ($category) {
            $q->where('id', $category->id);
        });
    })
    ->defaultSort('-created_at')
    ->allowedSorts('name','created_at','price')
    ->allowedFilters($allowedFilters)
    ->paginate(30)->withQueryString();

@endphp

<div class="row g-4 g-xl-5 row-cols-1 row-cols-md-{{$columnCount}}">
    @foreach ($products as $key => $product)
    <div class="col">
        <x-cards.product-card :product="$product" :popup="$product->product_type->popup_products"/>
    </div>
    @endforeach
</div>
<div class="mt-5 mt-xl-7">
    {{ $products->links() }}
</div>