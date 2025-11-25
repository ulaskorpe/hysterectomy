@php
    
    $taxable_area = false;
    $tax_values = [];
    $productDiscounts = [];
    
    foreach ($cart->get('items') as $key => $item) {

        if( $item['extra_info']['tax'] ){

            $tax = $item['extra_info']['tax'];
            $tax_values[] = $item['total_price'] * $tax->percentage / 100;

        }

        $discount = null;
        if($item->get('applied_actions')){
            $discount = Arr::first($item->get('applied_actions'), function ($value, $key) {
                return $value['group'] === 'ProductDiscount';
            });
        }

        if( $discount ){
            $productDiscounts[] = $discount['amount'];
        }

    }

    if( count($tax_values) > 0 ){
        $taxable_area = true;
    }

@endphp

@if ($taxable_area)
<div class="d-flex align-items-center justify-content-between fs-xs fw-semibold">
    <span>{{__('Sertifika Bedeli')}}</span>
    <span class="text-end">{!! formatNumberToCurrency($cart->get('items_subtotal') - array_sum($tax_values)) !!}</span>
</div>
<div class="d-flex align-items-center justify-content-between fs-xs fw-semibold">
    <span>{{__('KDV')}}</span>
    <span class="text-end">{!! formatNumberToCurrency(array_sum($tax_values)) !!}</span>
</div>
@endif

@if (count($productDiscounts) > 0)
<div class="d-flex align-items-center justify-content-between fs-xs fw-semibold text-uppercase">
    <span>{{ __('Ara Toplam') }}:</span>
    <span class="text-end">{!! formatNumberToCurrency($cart->get('items_subtotal') + abs(array_sum($productDiscounts))) !!}</span>
</div>
<div class="d-flex align-items-center justify-content-between fs-xs fw-semibold text-uppercase">
    <span>{{ __('Ürün İndirimleri') }}:</span>
    <span class="text-end">{!! formatNumberToCurrency(array_sum($productDiscounts)) !!}</span>
</div>
@else
<div class="d-flex align-items-center justify-content-between fs-xs fw-semibold text-uppercase">
    <span>{{ __('Ara Toplam') }}:</span>
    <span class="text-end">{!! formatNumberToCurrency($cart->get('items_subtotal')) !!}</span>
</div>
@endif

@if (count($cart->get('applied_actions')) > 0)
@foreach ($cart->get('applied_actions') as $action)

<div class="d-flex align-items-center justify-content-between fs-xs fw-semibold text-success">
    @if ($action['group'] == 'CouponDiscount')
    <span>{{ __('Kupon İndirimi') }}</span>
    @elseif ($action['group'] == 'GiftVoucher')
    <span>{{ __('Hediye Çeki') }}</span>
    @else
    <span>{{$action['title']}}</span>
    @endif
    <span class="text-end">{!! formatNumberToCurrency($action['amount']) !!}</span>
</div>

@endforeach
@endif

{{--
@if ($cart->get('actions_amount'))
<div class="d-flex flex-row align-items-center justify-content-between mt-1">
    <span>İndirimler:</span>
    <span class="text-end">&#x20BA; {{$cart->get('actions_amount')}}</span>
</div>
@endif
--}}

<div class="d-flex align-items-center justify-content-between fw-bold text-uppercase mt-2 pt-2 border-top">
    <span>{{ __('Toplam') }}:</span>
    <span class="text-end">{!! formatNumberToCurrency($cart->get('total')) !!}</span>
</div>