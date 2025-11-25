<x-fe-layout>

    <x-page-header-static :title="__('Ödeme')" />

    <div class="content-section">

        <div class="container">
            <div class="row g-3 g-xl-5 justify-content-end">
                
                <div class="col-lg-4">
                    <x-cart-coupon-gift-voucher :cart="$cart" />

                    <div class="border px-3 pt-3 overflow-hidden rounded sticky-top" style="top:100px">
                        <h2 class="p-responsive text-primary fw-bold">{{__('Sertifika Detayları')}}</h2>

                        <x-cart-mini :removeButtons="false"></x-cart-mini>

                    </div>
                </div>

                <div class="col-lg-8">
                    <form class="needs-validation" novalidate action="{{route('payment.pay')}}" method="POST">
                        @csrf
                        @honeypot
                        <div class="bg-gray-50 p-3 rounded border">
                            <div class="d-flex flex-row align-items-center justify-content-between mb-4">
                                <h2 class="h6 mb-0 fw-semibold">{{__('Alıcı Adresi')}}</h2>
                                <button type="button" class="btn btn-success btn-sm" data-address-title="{{__('Yeni Adres Ekle')}}" onclick="addNewAdress(this)">{{__('Yeni Ekle')}}</button>
                            </div>
                            @if ($addresses)
                            <div class="row g-3">
                                @foreach ($addresses as $adres)
                                <div class="col-12">
                                    <input 
                                        class="btn-check variant-check" 
                                        type="radio" name="shipping_id" 
                                        value="{{ $adres->id }}" 
                                        id="shipping-{{$adres->id}}" required 
                                        @checked(old('shipping_id'))
                                    />
                                    <label class="btn btn-white text-dark w-100 px-3 border" for="shipping-{{$adres->id}}">
                                        <div class="d-flex text-start">
                                            <i class="bi bi-circle fs-4"></i>
                                            <i class="bi bi-check-circle-fill fs-4"></i>
                                            <div class="ms-3 flex-grow-1 mt-1">
                                                <div class="d-flex align-items-center gap-3 justify-content-between">
                                                    <span class="mb-0 fw-bold">{{$adres->title}}</span>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <button type="button" class="btn btn-sm p-0 border-0 text-primary" onclick="updateAddress(this)" data-address-title="{{$adres->title}}" data-address-id="{{$adres->id}}"><i class="bi bi-pen"></i></button>
                                                    </div>
                                                </div>
                                                <hr class="my-2" />
                                                <div class="row g-3 fs-xs">
                                                    <div class="col-md-6 d-flex flex-column">
                                                        <span>{{$adres->name}}</span>
                                                        <span>{{$adres->email}}</span>
                                                        <span>{{$adres->phone}}</span>
                                                    </div>
                                                    <div class="col-md-6 d-flex flex-column">
                                                        <span>{{$adres->address}}</span>
                                                        <span>{{ $adres->county.' '.($adres->state ? $adres->state->name : '').' '.$adres->country->native}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                    @if ($loop->last)
                                    <div class="invalid-feedback alert alert-danger mt-2 mb-0 p-2">{{ __('Lütfen alıcı adresi seçiniz.') }}</div>
                                    @endif
                                </div>
                                @endforeach

                            </div>
                            @endif
                        </div>

                        <div class="bg-gray-50 p-3 rounded mt-4 border">
                            <div class="d-flex flex-row align-items-center justify-content-between mb-4">
                                <h2 class="h6 mb-0 fw-semibold">{{__('Fatura Adresi')}}</h2>
                                <button type="button" class="btn btn-success btn-sm" data-address-title="{{__('Yeni Adres Ekle')}}" onclick="addNewAdress(this)">{{__('Yeni Ekle')}}</button>
                            </div>
                            @if ($addresses)
                            @php
                                $billingAddresses = $addresses->filter(function($add){
                                    return $add->use_for_billing;
                                });
                            @endphp
                            <div class="row g-3">
                                @foreach ($billingAddresses as $adres)
                                <div class="col-12">
                                    <input 
                                        class="btn-check variant-check" 
                                        type="radio" name="billing_id" 
                                        value="{{ $adres->id }}" 
                                        id="billing-{{$adres->id}}" required 
                                        @checked(old('billing_id'))
                                    />
                                    <label class="btn btn-white text-dark w-100 px-3 border" for="billing-{{$adres->id}}">
                                        <div class="d-flex text-start">
                                            <i class="bi bi-circle fs-4"></i>
                                            <i class="bi bi-check-circle-fill fs-4"></i>
                                            <div class="ms-3 flex-grow-1 mt-1">
                                                <div class="d-flex align-items-center gap-3 justify-content-between">
                                                    <span class="mb-0 fw-bold">{{$adres->title}}</span>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <button type="button" class="btn btn-sm p-0 border-0 text-primary" onclick="updateAddress(this)" data-address-title="{{$adres->title}}" data-address-id="{{$adres->id}}"><i class="bi bi-pen"></i></button>
                                                    </div>
                                                </div>
                                                <hr class="my-2" />
                                                <div class="row g-3 fs-xs">
                                                    <div class="col-md-6 d-flex flex-column">
                                                        <span>{{$adres->name}}</span>
                                                        <span>{{$adres->email}}</span>
                                                        <span>{{$adres->phone}}</span>
                                                    </div>
                                                    <div class="col-md-6 d-flex flex-column">
                                                        <span>{{$adres->address}}</span>
                                                        <span>{{$adres->county.' '.($adres->state ? $adres->state->name : '').' '.$adres->country->native}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                    @if ($loop->last)
                                    <div class="invalid-feedback alert alert-danger mt-2 mb-0 p-2">{{ __('Lütfen fatura adresi seçiniz.') }}</div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>

                        @if ($cart->total == 0)
                        <input type="hidden" name="payment_type" value="free">
                        @else
                        <input type="hidden" name="payment_type" value="iyzico">
                        @endif
                        
                        <div class="border-top border-bottom py-3 my-4">
                            <x-mesafeli-checkbox />
                        </div>

                        <div class="mb-4">
                            <label class="fw-semibold">{{ __('Sipariş Notunuz') }}</label>
                            <textarea class="form-control" name="notes"></textarea>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary btn-lg d-flex align-items-center gap-2 ms-auto">
                                {{__('ÖDEMEYİ TAMAMLA')}}
                                <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>

                    </form>
                </div>
                
            </div>
        </div>


    </div>

</x-fe-layout>