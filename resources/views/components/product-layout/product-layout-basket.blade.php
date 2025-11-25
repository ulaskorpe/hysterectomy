@php

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
                $scale = $scaleValue['name'];
            }
        }
    }

@endphp

<form class="add-to-cart-form needs-validation" novalidate method="POST" action="{{route('cart.add-item',['direct_to_payment' => 'true'])}}" onSubmit="this.querySelector('[type=submit]').disabled=true;">

    @csrf
    @honeypot

    <div class="d-flex flex-column gap-2 mb-3">
        <div class="input-group">
            <span class="input-group-text">{{ __('Tüketim Miktarı') }}</span>
            <input type="number" class="form-control fw-bold" minlength="1" min="1" value="1" required name="quantity">
            @if (!empty($scale))
            <span class="input-group-text">{{ $scale }}</span>
            @endif
        </div>
    </div>

    @if (count($product->product_type->product_customer_attributes) > 0)
    <div class="bg-gray-50 border rounded p-3 mb-5">
        <x-product-layout.product-layout-customer-attributes :product-customer-attributes="$product->product_type->product_customer_attributes" />
    </div>
    @endif

    <input type="hidden" name="product_id" value="{{$product->id}}"/>

    <button class="btn btn-secondary btn-lg rounded-pill w-100 btn-add-to-cart" type="submit"><i class="bi bi-basket2 me-2"></i>{{ __('SERTİFİKA AL') }}</button>

</form>