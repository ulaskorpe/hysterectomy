@php

    $show_coupon_module = true;
    $active_coupon = null;

    $active_campaign = Arr::first($cart->get('applied_actions')->toArray(), function ($value, $key) {
        return $value['group'] === "CampaignDiscount";
    });

    if( $active_campaign ){

        $show_coupon_module = false;

    } else {

        $active_coupon = Arr::first($cart->get('applied_actions')->toArray(), function ($value, $key) {
            return $value['group'] === "CouponDiscount";
        });
    }

@endphp

@if ($show_coupon_module)
    
    @if ($active_coupon)

    <div @class(['d-flex flex-column fs-xs mb-3 p-3 rounded bg-opacity-10 bg-success border border-success gap-1'])>
        <span class="fw-bold">{{ __('Kupon İndirimi') }}</span>
        <div class="d-flex align-items-center gap-2">
            <form class="needs-validation" novalidate method="POST" action="{{route('cart.remove-coupon')}}" onsubmit="FreezeUI({text:' '});this.querySelector('[type=submit]').disabled=true;">
                @csrf
                @honeypot
                <input type="hidden" class="form-control" name="hash" value="{{$active_coupon['hash']}}">
                <button class="btn btn-sm text-danger p-0 border-0" type="submit" data-bs-toggle="tooltip" title="Çıkar"><i class="bi bi-x-lg"></i></button>
            </form>
            <span>{{$active_coupon['title']}}</span>
            <span class="fw-bold ms-auto">{!! formatNumberToCurrency($active_coupon['amount']) !!}</span>
        </div>
    </div>

    @else

    <div class="p-3 rounded border mb-3">
        <p class="mb-1 fw-semibold fs-xs">{{ __('İndirim kuponu kullan') }}</p>
        <form class="needs-validation" novalidate method="POST" action="{{route('cart.apply-coupon')}}" onsubmit="this.querySelector('[type=submit]').disabled=true;">
            @csrf
            @honeypot
            <div class="input-group input-group-sm">
                <input type="text" class="form-control px-2" placeholder="{{__('Kupon Kodu')}}" name="code" required>
                <button class="btn btn-success" type="submit">{{__('Uygula')}}</button>
            </div>
        </form>
    </div>

    @endif

@endif