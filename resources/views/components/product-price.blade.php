<div class="d-flex flex-row align-items-center mb-1">
@if ($product->product_type->is_cartable)

    @if ($product->use_option_group && $product->product_type->option_group && $product->product_type->option_group->has_own_price)
        
        @php
            $min = $product->product_variants->pluck('price')->filter()->sortBy('final_price')->first();
            $max = $product->product_variants->pluck('price')->filter()->sortByDesc('final_price')->first();
        @endphp

        @if ($min->final_price === $max->final_price)
            @if ($min->final_price < $min->price && $max->final_price < $max->price)
                <small class="text-muted me-2"><del>{!! formatNumberToCurrency($min->price) !!} <span class="px-1 fw-light">~</span> {!! formatNumberToCurrency($max->price) !!}</del></small>
            @endif
            <span class="fw-semibold text-primary">{!! formatNumberToCurrency($min->final_price) !!}</span>
        @else
            @if ($min->final_price < $min->price && $max->final_price < $max->price)
                <small class="text-muted me-2"><del>{!! formatNumberToCurrency($min->price) !!} <span class="px-1 fw-light">~</span> {!! formatNumberToCurrency($max->price) !!}</del></small>
            @endif
            <span class="fw-semibold text-primary p-responsive">{!! formatNumberToCurrency($min->final_price) !!} <span class="px-1 fw-light">~</span> {!! formatNumberToCurrency($max->final_price) !!}</span>
        @endif

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


        @if ($product->product_price->discount)
            <span class="text-muted me-2 p-responsive"><del>{!! formatNumberToCurrency($product->product_price->price) !!}</del></span>
        @endif
        <span class="fw-normal text-secondary lead-responsive">{!! formatNumberToCurrency($product->product_price->final_price) !!}{!! $scale !!}</span>

    @endif

@endif
</div>