<div class="container">
    <form class="needs-validation" novalidate method="post" action="{{ route('adres.store') }}">
        @csrf
        @honeypot
        @method('POST')

        <div class="row g-3 g-xl-4 mb-3 align-items-center">
            <label class="col-md-4 col-lg-3 fw-semibold">{{ __('Adres Adı') }}</label>
            <div class="col-md-8 col-lg-9">
                <input type="text" class="form-control" name="title" value="{{old('title')}}" autocomplete="title" required/>
            </div>
        </div>

        <div class="row g-3 g-xl-4 mb-3 align-items-center">
            <label class="col-md-4 col-lg-3 fw-semibold">{{__('Adı Soyadı')}}</label>
            <div class="col-md-8 col-lg-9">
                <input type="text" class="form-control name-input" name="name" value="{{old('name')}}" autocomplete="name" required/>
                <div class="invalid-feedback">{{ __('Ad soyad alanı en az 2 kelime olmalıdır.') }}</div>
            </div>
        </div>

        <div class="row g-3 g-xl-4 mb-3 align-items-center">
            <label class="col-md-4 col-lg-3 fw-semibold">{{__('E-Posta')}}</label>
            <div class="col-md-8 col-lg-9">
                <input type="email" class="form-control" name="email" value="{{old('email')}}" autocomplete="email" required/>
            </div>
        </div>

        <div class="row g-3 g-xl-4 mb-3 align-items-center">
            <label class="col-md-4 col-lg-3 fw-semibold">{{__('Telefon')}}</label>
            <div class="col-md-8 col-lg-9">
                <input type="text" class="form-control" name="phone" value="{{old('phone')}}" autocomplete="phone" required/>
            </div>
        </div>

        @php
            $guid = Str::uuid();
        @endphp

        <div class="row g-3 g-xl-4 mb-3 align-items-center">
            <label class="col-md-4 col-lg-3 fw-semibold">{{__('Ülke')}}</label>
            <div class="col-md-8 col-lg-9">
                <x-country-select :guid="$guid" />
            </div>
        </div>

        <div class="row g-3 g-xl-4 mb-3 align-items-center">
            <label class="col-md-4 col-lg-3 fw-semibold">{{__('Şehir')}}</label>
            <div class="col-md-8 col-lg-9">
                <x-state-select :guid="$guid" />
            </div>
        </div>

        <div class="row g-3 g-xl-4 mb-3 align-items-center">
            <label class="col-md-4 col-lg-3 fw-semibold">{{__('İlçe')}}</label>
            <div class="col-md-8 col-lg-9">
                <input type="text" class="form-control" name="county" value="{{old('county')}}" autocomplete="county" required/>
            </div>
        </div>

        <div class="row g-3 g-xl-4 align-items-center">
            <label class="col-md-4 col-lg-3 fw-semibold">{{__('Adres')}}</label>
            <div class="col-md-8 col-lg-9">
                <textarea class="form-control" name="address" required>{{old('address')}}</textarea>
            </div>
        </div>

        <hr class="my-4">

        <div class="row g-3 g-xl-4 align-items-center">
            <label class="col-md-4 col-lg-3 fw-semibold"></label>
            <div class="col-md-8 col-lg-9">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="use-for-billing-new" name="use_for_billing" onchange="useForBilling(this);">
                    <label class="form-check-label" for="use-for-billing-new">{{__('Bu adresi fatura bilgilerimde de kullan')}}</label>
                </div>
            </div>
        </div>
        

        <div @class(['for-billing d-none'])>
            <hr class="my-4">

            <input type="hidden" name="billing_type" value="Kurumsal">

            <div @class(['row g-3 g-xl-4 align-items-center billing-details-kurumsal'])>

                <div class="col-12">
                    <div class="row g-3 g-xl-4 mb-3 align-items-center">
                        <label class="col-md-4 col-lg-3 fw-semibold">{{__('Firma Adı')}}</label>
                        <div class="col-md-8 col-lg-9">
                            <input type="text" class="form-control" name="company_name" value="{{old('company_name')}}" autocomplete="company_name" required/>
                        </div>
                    </div>
                    <div class="row g-3 g-xl-4 mb-3 align-items-center">
                        <label class="col-md-4 col-lg-3 fw-semibold">{{__('Vergi Dairesi')}}</label>
                        <div class="col-md-8 col-lg-9">
                            <input type="text" class="form-control" name="vd" value="{{old('vd')}}" autocomplete="vd" required/>
                        </div>
                    </div>
                    <div class="row g-3 g-xl-4 mb-3 align-items-center">
                        <label class="col-md-4 col-lg-3 fw-semibold">{{__('Vergi no')}}</label>
                        <div class="col-md-8 col-lg-9">
                            <input type="text" class="form-control" name="vkn" value="{{old('vkn')}}" autocomplete="vkn" required/>
                        </div>
                    </div>
                    <div class="row g-3 g-xl-4 align-items-center">
                        <label class="col-md-4 col-lg-3 fw-semibold"></label>
                        <div class="col-md-8 col-lg-9">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="e-fatura-mukellef-new" name="e_fatura" @checked(old('e_fatura'))>
                                <label class="form-check-label" for="e-fatura-mukellef-new">{{__('E-fatura mükellefiyim')}}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4">

        <div class="row g-3 g-xl-4 align-items-center justify-content-end">
            <div class="col-md-8 col-lg-9">
                <button class="btn btn-success" type="submit">{{__('Kaydet')}}</button>
            </div>
        </div>

    </form>
</div>