@php
    $variant_selects = [];
    $queryParams = [];
    $option_selected_count = 0;

    $minMaxPrices = $product->product_variants()
    ->join('product_prices', 'product_variants.id', '=', 'product_prices.priceable_id')
    ->where('product_prices.priceable_type','App\Models\ProductVariant')
    ->whereNull('product_variants.deleted_at')
    ->selectRaw('MIN(product_prices.price) as min_price,MIN(product_prices.final_price) as min_final_price, MAX(product_prices.price) as max_price, MAX(product_prices.final_price) as max_final_price')
    ->first();

    $minPrice = formatNumberToCurrency($minMaxPrices->min_price);
    $maxPrice = formatNumberToCurrency($minMaxPrices->max_price);
    $minFinalPrice = formatNumberToCurrency($minMaxPrices->min_final_price);
    $maxFinalPrice = formatNumberToCurrency($minMaxPrices->max_final_price);


    $product_type_option_group = $product->product_type->option_group;
    $product_type_options = $product_type_option_group->options;
    
    $variants_query = $product->product_variants()->with('price')->where('product_id',$product->id);
                
    if($product->product_variants && $product->product_variants->count() > 0){
    
        foreach ($product_type_options as $key => $option) {
            $variant_selects[$option->id] = [
                'id' => $option->id,
                'name' => $option->name,
                'option_type' => $option->option_type,
                'fe_visible' => $option->fe_visible,
                'slug' => $option->slug,
                'values' => []
            ];

            $queryParams[$option->slug] = request()->input($option->slug) ?? null;

            if( $queryParams[$option->slug] && $queryParams[$option->slug] != "null" ){

                $variants_query->whereJsonContains('option_values',[['slug' => $option->slug]])->whereJsonContains('option_values',[['value' => $queryParams[$option->slug]]]);

                $option_selected_count++;
            }
            
        }

        $variants = $variants_query->get();

        $today = today();
        $now = now();
        
        $option_date = $today;

        foreach ($variants as $key => $variant) {
            
            foreach ($variant->option_values as $o => $option) {
                
                if( $option['option_type'] == 'datetime'){

                    if( is_array($option['value']) ){
                        foreach ($option['value'] as $val) {
                            if( $now <= \Carbon\Carbon::parse($val)->subHours(1)->format('Y-m-d H:i:s') ){
                                $variant_selects[$option['id']]['values'][] = $val;
                            }
                        }
                    } else {
                        if( $now < \Carbon\Carbon::parse($option['value'])->subHours(1)->format('Y-m-d H:i:s') ){
                            $variant_selects[$option['id']]['values'][] = $option['value'];
                        }
                    }

                } else if( $option['option_type'] == 'date'){

                    if( is_array($option['value']) ){
                        foreach ($option['value'] as $val) {
                            if( $today <= \Carbon\Carbon::parse($val) ){
                                $variant_selects[$option['id']]['values'][] = $val;
                            }
                        }
                    } else {
                        if( $today <= \Carbon\Carbon::parse($option['value']) ){
                            $variant_selects[$option['id']]['values'][] = $option['value'];
                        }
                    }

                } else {

                    if( is_array($option['value']) ){
                        foreach ($option['value'] as $val) {
                            $variant_selects[$option['id']]['values'][] = $val;
                        }
                    } else {
                        $variant_selects[$option['id']]['values'][] = $option['value'];
                    }

                }

                $variant_selects[$option['id']]['values'] = array_values(array_unique($variant_selects[$option['id']]["values"]));
            }

        }


    }

@endphp

<div id="product-variant-selections" class="mb-5 rounded p-3 bg-gray-50 border">

    @foreach ($variant_selects as $variant)
    <div @class(['row g-1 align-items-center','mt-3' => !$loop->first])>
        <label class="col-lg-4 fw-bold">{{ $variant['name'] }} seçiniz</label>
        <div class="col-lg-8">
            <select class="form-control" required onchange='variantSelection(@json($product),this,@json($queryParams))' data-slug="{{$variant['slug']}}" name="selections[]">
                <option value="" disabled selected>{{ $variant['name'] }} seçiniz</option>
                @foreach ($variant['values'] as $d => $val)
                <option value="{{$val}}"{{ request()->input($variant['slug']) == $val ? ' selected' : '' }}>{{$val}}</option>
                @endforeach
            </select>
        </div>
    </div>
    @endforeach

    @if ($option_selected_count > 0)
        <hr class="my-3" />
        <div class="hstack gap-2 align-items-center justify-content-between">
            <button type="button" class="btn btn-sm text-danger p-0" onclick='variantSelection(@json($product),null,null)'><i class="bi bi-x-lg me-2"></i>Seçimleri Temizle</button>
            @if ($product_type_option_group->has_own_price && $product_type_options->count() == $option_selected_count && $minFinalPrice != $maxFinalPrice)
                <div>
                    @if ($variants[0]->price->discount)
                        <span class="text-muted me-2 p-responsive"><del>&#x20BA;{{$variants[0]->price->price}}</del></span>
                    @endif
                    <span class="lead-responsive lh-1">&#x20BA; <span id="variant-price" class="fw-bolder text-dark">{{$variants[0]->price->final_price}}</span></span>
                </div>
            @endif
        </div>
    @endif

    <input type="hidden" name="is_variant" value="{{true}}"/>
    <input type="hidden" name="variant_id" value="{{ $product_type_options->count() == $option_selected_count ? $variants[0]->id : '' }}"/>
</div>