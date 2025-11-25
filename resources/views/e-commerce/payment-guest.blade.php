<x-fe-layout>

    <x-page-header-static :title="__('Ödeme')" />

    <div class="content-section">
        <div class="container">
            <div class="row g-4 g-xl-5 justify-content-end">

                <div class="col-lg-4">

                    <x-cart-coupon-gift-voucher :cart="$cart" />
                    <div class="border px-3 pt-3 overflow-hidden rounded sticky-top" style="top:100px">
                        <h2 class="p-responsive text-primary fw-bold">{{__('Sertifika Detayları')}}</h2>
                        <x-cart-mini :removeButtons="false"></x-cart-mini>
                    </div>

                </div>

                <div class="col-lg-8">

                    <form class="needs-validation" novalidate action="{{route('payment.pay')}}" method="POST" id="payment-guest-form">
                        @csrf
                        @honeypot

                        <div class="bg-gray-50 p-3 rounded border">
                            <h2 class="h6 mb-4">{{ __('Alıcı Adresi') }}</h2>
                            <div class="row g-3">
                                <div class="col-lg-12">
                                    <label class="form-label fw-semibold fs-sm">{{__('Alıcı Adı Soyadı')}}</label>
                                    <input type="text" class="form-control bg-white name-input" name="shipping[name]" value="{{old('shipping.name')}}" required/>
                                    <div class="invalid-feedback">{{ __('Ad soyad alanı en az 2 kelime olmalıdır.') }}</div>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label fw-semibold fs-sm">{{__('E-Posta')}}</label>
                                    <input type="email" class="form-control bg-white" name="shipping[email]" value="{{old('shipping.email')}}" required/>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label fw-semibold fs-sm">{{__('Telefon')}}</label>
                                    <input type="text" class="form-control bg-white" name="shipping[phone]" value="{{old('shipping.phone')}}" required/>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label fw-semibold fs-sm">{{__('Ülke')}}</label>
                                    <input class="form-control bg-white" name="shipping[country]" value="{{old('shipping.country', 'Türkiye')}}" required readonly/>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label fw-semibold fs-sm">{{__('Şehir')}}</label>
                                    <input class="form-control bg-white" name="shipping[state]" value="{{old('shipping.state')}}" required/>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label fw-semibold fs-sm">{{__('İlçe')}}</label>
                                    <input class="form-control bg-white" name="shipping[county]" value="{{old('shipping.county')}}" required/>
                                </div>
                                <div class="col-lg-12">
                                    <label class="form-label fw-semibold fs-sm">{{__('Adres')}}</label>
                                    <textarea class="form-control bg-white" name="shipping[address]" required>{{old('shipping.address')}}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" role="switch" id="billing-address-switch" checked name="billing_same">
                            <label class="form-check-label" for="billing-address-switch">{{__('Fatura adresi alıcı adresiyle aynı')}}</label>
                        </div>

                        <div class="bg-gray-50 p-3 rounded border mt-4">
                            <div class="row g-3 g-xl-4 billing-details-kurumsal">
                                <div class="col-lg-12">
                                    <label class="form-label fw-semibold fs-sm">{{__('Firma Adı')}}</label>
                                    <input type="text" class="form-control bg-white" name="company_name" value="{{old('company_name')}}" autocomplete="company_name" required/>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label fw-semibold fs-sm">{{__('Vergi Dairesi')}}</label>
                                    <input type="text" class="form-control bg-white" name="vd" value="{{old('vd')}}" autocomplete="vd" required/>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label fw-semibold fs-sm">{{__('Vergi no')}}</label>
                                    <input type="text" class="form-control bg-white" name="vkn" value="{{old('vkn')}}" autocomplete="vkn" required/>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="e-fatura-mukellef" name="e_fatura">
                                        <label class="form-check-label" for="e-fatura-mukellef">{{__('E-fatura mükellefiyim')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-3 rounded border mt-4 d-none" id="billing-address-details">
                            <h2 class="h6 mb-4">{{__('Fatura Bilgileri')}}</h2>
                            <div class="row g-3 g-xl-4">
                                <div class="col-lg-12">
                                    <label class="form-label fw-semibold fs-sm">{{__('Alıcı Adı Soyadı')}}</label>
                                    <input type="text" class="form-control bg-white name-input" name="billing[name]" value="{{old('billing.name')}}"/>
                                    <div class="invalid-feedback">{{ __('Ad soyad alanı en az 2 kelime olmalıdır.') }}</div>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label fw-semibold fs-sm">{{__('E-Posta')}}</label>
                                    <input type="email" class="form-control bg-white" name="billing[email]" value="{{old('billing.email')}}"/>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label fw-semibold fs-sm">{{__('Telefon')}}</label>
                                    <input type="text" class="form-control bg-white" name="billing[phone]" value="{{old('billing.phone')}}"/>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label fw-semibold fs-sm">{{__('Ülke')}}</label>
                                    <input class="form-control bg-white" name="billing[country]" value="{{old('billing.country', 'Türkiye')}}" readonly/>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label fw-semibold fs-sm">{{__('Şehir')}}</label>
                                    <input class="form-control bg-white" name="billing[state]" value="{{old('billing.state')}}"/>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label fw-semibold fs-sm">{{__('İlçe')}}</label>
                                    <input class="form-control bg-white" name="billing[county]" value="{{old('billing.county')}}"/>
                                </div>
                                <div class="col-lg-12">
                                    <label class="form-label fw-semibold fs-sm">{{__('Adres')}}</label>
                                    <textarea class="form-control bg-white" name="billing[address]">{{old('billing.address')}}</textarea>
                                </div>
                            </div>
                        </div>

                        @if ($cart->total == 0)
                        <input type="hidden" name="payment_type" value="free">
                        @else
                        <input type="hidden" name="payment_type" value="iyzico">
                        @endif

                        <input type="hidden" name="billing_type" value="Kurumsal">
                        
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

    <x-slot name="footerScripts">
        
        <script>
            const form = document.getElementById('payment-guest-form');
            const billingSwitch = document.getElementById('billing-address-switch');
            const billingDetails = document.getElementById('billing-address-details');

            billingSwitch.addEventListener('change',function(e){
                billingDetails.classList.toggle('d-none');

                if( billingSwitch.checked ){

                    billingDetails.querySelectorAll('input').forEach(input => {
                        input.removeAttribute('required');
                    });
                    billingDetails.querySelectorAll('select').forEach(select => {
                        select.removeAttribute('required');
                    });
                    billingDetails.querySelectorAll('textarea').forEach(textarea => {
                        textarea.removeAttribute('required');
                    });

                    var tcNoInput = form.querySelector('[name="tc_no"]');
                    var nameInputs = form.querySelectorAll('.name-input');

                    if(tcNoInput){
                        tcNoInput.setCustomValidity('');
                    }

                    if( nameInputs.length > 0 ){
                        nameInputs.forEach(nameInput => {
                            nameInput.setCustomValidity('');
                        });
                    }

                    return;
                }

                billingDetails.querySelectorAll('input').forEach(input => {
                    input.setAttribute('required','required');
                });
                billingDetails.querySelectorAll('select').forEach(select => {
                    select.setAttribute('required','required');
                });
                billingDetails.querySelectorAll('textarea').forEach(textarea => {
                    textarea.setAttribute('required','required');
                });
            });
        </script>

    </x-slot>

</x-fe-layout>