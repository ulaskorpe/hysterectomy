@php
    //dd($cart->get('items'));
@endphp

<x-fe-layout>

   <x-page-header-static :title="__('Sepetim')" />

    <section class="content-section">
        @if ($cart->get('items')->count() > 0)
        
        <div class="container">
            <div class="row g-3 g-xl-5">
                <div class="col-lg-8 col-xl-9">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Ürün</th>
                                    <th>Birim Fiyat</th>
                                    <th>Toplam</th>
                                    <th class="text-end">Adet</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cart->get('items') as $item)

                                @php
                                    
                                    $itemModel = $item->get('extra_info')['variant_details'] ?? $item->get('extra_info')['product_details'];
                                    $maxQuantity = $itemModel['incart']['active_stock'];

                                    $discount = null;

                                    if($item->get('applied_actions')){
                                        $discount = Arr::first($item->get('applied_actions'), function ($value, $key) {
                                            return $value['group'] === 'ProductDiscount';
                                        });
                                    }

                                @endphp

                                <tr>
                                    <td>
                                        <div class="d-flex gap-2 text-nowrap">
                                            @if ($item->get('extra_info')['main_image'])
                                            <img src="{{ $item->get('extra_info')['main_image']['preview_url'] }}" class="box-75 rounded me-2" />
                                            @endif
                                            <div class="d-flex flex-column">
                                                <span class="fw-semibold fs-sm">{{ $item->title }}</span>
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
                                    </td>                                    
                                    <td class="text-nowrap">
                                        <div class="d-flex flex-column">
                                            @if ($discount)
                                            <span class="fs-xs text-muted">
                                                <del>{{formatNumberToCurrency(($item->get('subtotal') / $item->get('quantity')) + (abs($discount['amount']) / $item->get('quantity')))}}</del>
                                            </span>
                                            <span class="fw-bold">{{formatNumberToCurrency($item->get('subtotal') / $item->get('quantity'))}}</span>
                                            @else
                                            <span class="fw-bold">{{formatNumberToCurrency($item->price)}}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-nowrap">
                                        <div class="d-flex flex-column">
                                            @if ($discount)
                                            <span class="fs-xs text-muted"><del>{{formatNumberToCurrency($item->get('subtotal') + abs($discount['amount']))}}</del></span>
                                            @endif
                                            <span class="fw-bold">{{formatNumberToCurrency($item->get('subtotal'))}}</span>
                                        </div>
                                    </td>
                                    <td id="quantity-{{$item->get('hash')}}">
                                        
                                        <div class="h-40px hstack gap-2 align-items-center justify-content-between mw-100px ms-auto">

                                            @if ($item->get('quantity') == 1)
                                            <form action="{{route('cart.remove',$item->get('hash'))}}" method="post">
                                                @csrf
                                                @honeypot
                                                <span class="btn btn-sm btn-danger rounded-circle p-0 box-30 d-flex align-items-center justify-content-center" onclick="FreezeUI({text:' '});this.closest('form').submit()">
                                                    <i class="bi bi-trash"></i>
                                                </span>
                                            </form>
                                            @endif
                                        
                                            @if ($item->get('quantity') > 1)
                                            <form action="{{route('cart.change.quantity',[$item->get('hash'),$item->get('quantity') - 1])}}" method="post">
                                                @csrf
                                                @honeypot
                                                <span class="btn btn-sm btn-light rounded-circle p-0 box-30 d-flex align-items-center justify-content-center" onclick="FreezeUI({text:' '});this.closest('form').submit()">
                                                    <i class="bi bi-dash-lg"></i>
                                                </span>
                                            </form>
                                            @endif

                                            <small class="px-1 fw-semibold">{{ $item->get('quantity') }}</small>
                                        
                                            @if ($maxQuantity !== null && $maxQuantity === 0)
                                            <span class="text-muted cursor-pointer disabled" data-bs-toggle="tooltip" title="Bu üründen en fazla {{$item->get('quantity')}} adet alabilirsiniz.">
                                                <i class="bi bi-plus-lg"></i>
                                            </span>
                                            @else
                                            <form action="{{route('cart.change.quantity',[$item->get('hash'),$item->get('quantity') + 1])}}" method="post">
                                                @csrf
                                                @honeypot
                                                <span class="btn btn-sm btn-light rounded-circle p-0 box-30 d-flex align-items-center justify-content-center" onclick="FreezeUI({text:' '});this.closest('form').submit()">
                                                    <i class="bi bi-plus-lg"></i>
                                                </span>
                                            </form>
                                            @endif
                                        
                                        </div>


                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-4 col-xl-3">

                    <x-cart-coupon-gift-voucher :cart="$cart" />

                    <x-cart-campaigns :cart="$cart" />

                    <div class="shadow p-3 rounded d-flex flex-column gap-3">

                        <div id="cart-totals-area">
                            <x-cart-totals />
                        </div>
                        
                        <a href="{{route('payment.index')}}" class="btn btn-primary">Ödemeye Geç</a>

                    </div>
                </div>
            </div>
        </div>

        @else

        <div class="container">
            <div class="alert alert-info">
                Sepetinizde ürün bulunmuyor.
            </div>
        </div>

        @endif
    </section>

</x-fe-layout>