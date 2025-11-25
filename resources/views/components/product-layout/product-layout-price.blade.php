@if ($product->use_option_group && $product->product_type->option_group && $product->product_type->option_group->has_own_price)
                    
@php

    // sorgu calistirmadan yapabileceim yontem buldum.
    // $minMaxPrices = $product->product_variants()
    // ->join('product_prices', 'product_variants.id', '=', 'product_prices.priceable_id')
    // ->where('product_prices.priceable_type','App\Models\ProductVariant')
    // ->whereNull('product_variants.deleted_at')
    // ->selectRaw('MIN(product_prices.price) as min_price,MIN(product_prices.final_price) as min_final_price, MAX(product_prices.price) as max_price, MAX(product_prices.final_price) as max_final_price')
    // ->first();

    // $minPrice = formatNumberToCurrency($minMaxPrices->min_price);
    // $maxPrice = formatNumberToCurrency($minMaxPrices->max_price);
    // $minFinalPrice = formatNumberToCurrency($minMaxPrices->min_final_price);
    // $maxFinalPrice = formatNumberToCurrency($minMaxPrices->max_final_price);

    $min = $product->product_variants->pluck('price')->filter()->sortBy('final_price')->first();
    $max = $product->product_variants->pluck('price')->filter()->sortByDesc('final_price')->first();

    $minPrice = formatNumberToCurrency($min->price);
    $maxPrice = formatNumberToCurrency($max->price);
    $minFinalPrice = formatNumberToCurrency($min->final_price);
    $maxFinalPrice = formatNumberToCurrency($max->final_price);
    
@endphp

<div class="d-flex align-items-end gap-3">
    @if ($minFinalPrice === $maxFinalPrice)
    <div class="d-flex align-items-end gap-3">
        @if ($minFinalPrice < $minPrice && $maxFinalPrice < $minPrice)
        <span class="text-muted"><del>{!! $minPrice !!} <span class="px-2 fw-light">~</span> {!! $minPrice !!}</del></span>
        @endif
        <span id="product-price" class="h4 mb-0 lh-1 fw-semibold">{!! $minFinalPrice !!}</span>
    </div>
    @else
    <div class="d-flex align-items-end gap-3">
        @if ($minFinalPrice < $minPrice && $maxFinalPrice < $minPrice)
        <span class="text-muted"><del>{!! $minPrice !!} <span class="px-2 fw-light">~</span> {!! $minPrice !!}</del></span>
        @endif
        <span id="product-price" class="h4 mb-0 lh-1 fw-semibold">{!! $minFinalPrice !!} <span class="px-2 fw-light">~</span> {!! $maxFinalPrice !!}</span>
    </div>
    @endif
</div>

@else

@php
    
    //Olcu birimi varsa fiyatın sonuna /birim seklinde ekleyecegiz.
    $scale = "";
    $hasScaleAttr = Arr::first($product->attributes, function ($value, $key) {
        return isset($value['unit_scale']) && $value['unit_scale'] === true;
    });

    if( $hasScaleAttr ){

        if( $hasScaleAttr['option_type'] == 'select' ){
            $scaleValue = Arr::first($hasScaleAttr['values'], function ($value, $key) use($hasScaleAttr) {
                return $value['id'] === $hasScaleAttr['value'];
            });

            if( $scaleValue ){
                $scale = '<small class="fs-sm fw-light"> /'.$scaleValue['name'].'</small>';
            }
        }
    }

@endphp

<div class="d-flex align-items-end gap-3">
    <span class="text-primary"><span id="product-price" class="h3 mb-0 lh-1 fw-bold">{!! formatNumberToCurrency($product->product_price->final_price) !!}</span>{!! $scale !!}</span>
    @if ($product->product_price->discount)
    <div class="d-flex gap-2 align-items-end">
        <span class="text-muted"><del>{!! formatNumberToCurrency($product->product_price->price) !!}</del></span>
        <div class="hstack align-items-center text-success">
            <span class="fw-semibold">
                {{$product->product_price->discount->discount_type == 'percentage' ? '%' : 'TL'}}{{$product->product_price->discount->value}}
            </span>
            <i class="bi bi-arrow-down-short"></i>
        </div>
    </div>
    @endif
</div>

@endif