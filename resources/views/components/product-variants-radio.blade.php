@php
    
    $today = today();
    $now = now();

    $event_date_expire = false;

    if($product->event_date){

        if($today > \Carbon\Carbon::parse($product->event_date)){
            $event_date_expire = true;
        }

    }

    $useStock = $product->product_type->option_group->stock_usage;
    $ownPrice = $product->product_type->option_group->has_own_price;

@endphp


<div id="product-variant-selections" class="mb-5">

    <h3 class="p-responsive text-info mb-3">{{ $product->product_type->option_group->name }}</h3>
    @foreach ($product->product_variants as $selectable)

        @php
            $visible_stock = true;
            $visible_datetime = true;
            $visible_date = true;
            $visible_time = true;
            $error_message = "";

            if($useStock){

                $maxQuantity = $selectable->stock;

                if ($selectable->incart['in_cart']) {
                    $maxQuantity = $selectable->incart['active_stock'];
                }

                if($maxQuantity < 1) {
                    $visible_stock = false;
                    $error_message = "Yeterli stok bulunmamaktadır.";
                }

            }

            $option_date = $product->event_date ? $product->event_date : $today;

            foreach ($selectable->option_values as $oo => $val) {

                if($event_date_expire) {

                    $visible_datetime = false;
                        $error_message = "Bu etkinliğin tarihi geçmiş.";

                } else {

                    if( $val['option_type'] == 'datetime'){

                        if( $now > \Carbon\Carbon::parse($val['value'])->subHours(1)->format('Y-m-d H:i:s') ){
                            $visible_datetime = false;
                            $error_message = "Bu seçeneğin tarihi geçmiş.";
                        }

                    }
                    if( $val['option_type'] == 'date'){

                        $option_date = \Carbon\Carbon::parse($val['value']);

                        if( $today > \Carbon\Carbon::parse($val['value']) ){
                            $visible_date = false;
                            $error_message = "bu seçeneğin tarihi geçmiş.";
                        }

                    } 
                    if( $val['option_type'] == 'time'){

                        $value_time = \Carbon\Carbon::parse($val['value'])->subHours(1)->format('H:i:s');
                        if( $option_date == $today && $now->format('H:i:s') > $value_time ){
                            $visible_time = false;
                            $error_message = "Bu seçeneğin saati geçmiş.";
                        }

                    }

                }

            }

        @endphp

        @if ($event_date_expire)
            
        <div class="bg-light p-3 rounded border mb-3">
            <div class="row g-3 align-items-center">
                <div class="col-lg-7">
                    <div class="row g-3">
                        @foreach ($selectable->option_values as $s => $value)
                            @if (isset($value['fe_visible']) && $value['fe_visible'])
                            <div class="col-md-6 col-lg-4">
                                <div @class(['d-flex','flex-column','text-start'])>
                                    <span class="lh-1 mb-1">{{$value['name']}}</span>
                                    <span class="fw-bold p-responsive lh-1">{{$value['value']}}</span>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="hstack gap-2 justify-content-lg-end align-items-center fw-bold">
                        <i class="bi bi-info-circle"></i>
                        <small>{{ __('Etkinlik tarihi geçmiş.') }}</small>
                    </div>
                </div>
            </div>
        </div>

        @elseif ($visible_stock && $visible_datetime && $visible_time && $visible_time)
        
        <div class="">
            <input 
                class="btn-check variant-check" 
                type="radio" name="variant_id" 
                value="{{ $selectable->id }}" 
                id="variant-{{$selectable->id}}" 
                data-stock="{{$selectable->stock}}" 
                onchange="variantPrepareAddToCartButton(this.value)" 
            />
            <label @class(['btn btn-outline-success w-100 px-3','mb-3' => !$loop->last]) for="variant-{{$selectable->id}}">
                <div class="d-flex flex-row align-items-center">
                    <i class="bi bi-circle fs-5"></i>
                    <i class="bi bi-check-circle fs-5"></i>
                    <div class="ms-3 d-flex flex-row align-items-center w-100 justify-content-between gap-3">
                        <div class="d-flex flex-grow-1">
                            <div class="row g-3 w-100 align-items-center">
                                @foreach ($selectable->option_values as $s => $value)
                                    @if (isset($value['fe_visible']) && $value['fe_visible'])
                                    <div class="col-md-6 col-lg">
                                        <div @class(['d-flex','flex-column','text-start'])>
                                            @if ($value['option_type'] == 'image')
                                            <a href="{{$value['value']}}" data-fancybox class="box box-50 rounded bg-center bg-cover mx-auto" data-bg-image="{{$value['value']}}" data-bs-toggle="tooltip" title="{{$value['name']}}"><span class="visually-hidden">{{$value['name']}}</span></a>
                                            @else
                                            <span class="lh-1 mb-1">{{$value['name']}}</span>
                                            <span class="fw-bold lh-1">{{$value['value']}}</span>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @if ($ownPrice && $selectable->price)
                        <div class="vstack justify-content-center align-items-end mw-100px">
                            @if ($selectable->price->final_price < $selectable->price->price)
                            <small class="text-muted p-responsive"><del>&#x20BA;{{$selectable->price->price}}</del></small>
                            @endif
                            <span class="fw-bold p-responsive lh-1">&#x20BA;{{$selectable->price->final_price}}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </label>
        </div>

        @else

        <div class="bg-light p-3 rounded border mb-3">
            <div class="row g-3 align-items-center">
                <div class="col-lg-7">
                    <div class="row g-3">
                        @foreach ($selectable->option_values as $s => $value)
                            @if (isset($value['fe_visible']) && $value['fe_visible'] && $value['option_type'] !== 'image')
                            <div class="col-md-6 col-lg-4">
                                <div @class(['d-flex','flex-column','text-start'])>
                                    <span class="lh-1 mb-1">{{$value['name']}}</span>
                                    <span class="fw-bold p-responsive lh-1">{{$value['value']}}</span>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="hstack gap-2 justify-content-lg-end align-items-center fw-bold">
                        <i class="bi bi-info-circle"></i>
                        <small>{{$error_message}}.</small>
                    </div>
                </div>
            </div>
        </div>

        @endif
    @endforeach

    <input type="hidden" name="is_variant" value="{{true}}"/>
    
</div>