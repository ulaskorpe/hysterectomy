@if ($cart->get('items')->count() > 0)
@foreach ($cart->get('items') as $item)

    @php

        $discount = null;

        if($item->get('applied_actions')){
            $discount = Arr::first($item->get('applied_actions'), function ($value, $key) {
                return $value['group'] === 'ProductDiscount';
            });
        }
        
    @endphp

    <div @class(['mb-3 pb-3 d-flex flex-column gap-2','border-bottom' => !$loop->last])>
        <div class="d-flex justify-content-between gap-3">
            <div class="d-flex gap-2">
                @if ($item->get('extra_info')['main_image'])
                <img src="{{ $item->get('extra_info')['main_image']['preview_url'] }}" class="box-75 rounded" />
                @endif
                <div class="d-flex flex-column">
                    <span class="fw-semibold fs-sm">{{ $item->title }} <span class="text-danger">x {{$item->quantity}}</span></span>
                    @if ($item->get('extra_info')['variant_details'])
                    <div class="vstack mt-1">
                        @foreach ($item->get('extra_info')['variant_details']['option_values'] as $option)
                        @if ($option['fe_visible'])
                        <div class="fs-xs">
                            <span>{{ $option['name'] }}: </span>
                            @if ($option['option_type'] == 'multiselect')
                            <span class="fw-semibold">{{ $option['value'][0] }}</span>
                            @else
                            <span class="fw-semibold">{{ $option['value'] }}</span>
                            @endif
                        </div>
                        @endif
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            <div class="d-flex flex-column text-end">
                <div class="d-flex flex-column">
                    @if ($discount)
                    <span class="fs-xs text-muted"><del>{!! formatNumberToCurrency($item->get('subtotal') + abs($discount['amount'])) !!}</del></span>
                    @endif
                    <span class="fw-bold">{!! formatNumberToCurrency($item['total_price']) !!}</span>
                </div>
                
                @if ($removeButtons)
                <div>
                    <button class="btn text-danger border-0 p-0" type="button" onclick="removeItemFromCart('{{$item->get('hash')}}')">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                @endif
            </div>
        </div>
        @if ($item->get('extra_info')['customer_attributes'] && count($item->get('extra_info')['customer_attributes']) > 0)
        <div class="d-flex flex-column gap-1">
            @foreach ($item->get('extra_info')['customer_attributes'] as $attr)
            <div class="vstack rounded p-2 bg-gray-50 border">
                <span class="fs-sm fw-semibold">{{ $attr['label'] }}:</span>
                <span class="fs-sm">{{ $attr['value'] }}</span>
            </div>
            @endforeach
        </div>
        @endif
    </div>

@endforeach

<div class="d-flex flex-column bg-light p-3 mx-n3">
    <x-cart-totals />
</div>

@else

<div class="alert alert-info">
    Sepetinizde ürün bulunmuyor..
</div>

@endif