<div class="container">
    <form class="needs-validation" novalidate method="post" action="{{ route('adres.update',$adres) }}">
        @csrf
        @honeypot
        @method('PUT')

        <div class="row g-3 g-xl-4 mb-3 align-items-center">
            <label class="col-md-4 col-lg-3 fw-semibold">{{__('Adres Adı')}}</label>
            <div class="col-md-8 col-lg-9">
                <input type="text" class="form-control" name="title" value="{{old('title',$adres->title)}}" autocomplete="title" required/>
            </div>
        </div>

        <div class="row g-3 g-xl-4 mb-3 align-items-center">
            <label class="col-md-4 col-lg-3 fw-semibold">{{__('Alıcı Adı Soyadı')}}</label>
            <div class="col-md-8 col-lg-9">
                <input type="text" class="form-control name-input" name="name" value="{{old('name',$adres->name)}}" autocomplete="name" required/>
                <div class="invalid-feedback">{{ __('Ad soyad alanı en az 2 kelime olmalıdır.') }}</div>
            </div>
        </div>

        <div class="row g-3 g-xl-4 mb-3 align-items-center">
            <label class="col-md-4 col-lg-3 fw-semibold">{{__('E-Posta')}}</label>
            <div class="col-md-8 col-lg-9">
                <input type="email" class="form-control" name="email" value="{{old('email', $adres->email)}}" autocomplete="email" required/>
            </div>
        </div>

        <div class="row g-3 g-xl-4 mb-3 align-items-center">
            <label class="col-md-4 col-lg-3 fw-semibold">{{__('Telefon')}}</label>
            <div class="col-md-8 col-lg-9">
                <input type="text" class="form-control" name="phone" value="{{old('phone', $adres->phone)}}" autocomplete="phone" required/>
            </div>
        </div>

        @php
            $guid = Str::uuid();
        @endphp

        <div class="row g-3 g-xl-4 mb-3 align-items-center">
            <label class="col-md-4 col-lg-3 fw-semibold">{{__('Ülke')}}</label>
            <div class="col-md-8 col-lg-9">
                <x-country-select :guid="$guid" :countryId="$adres->country_id"/>
            </div>
        </div>

        <div class="row g-3 g-xl-4 mb-3 align-items-center">
            <label class="col-md-4 col-lg-3 fw-semibold">{{__('Şehir')}}</label>
            <div class="col-md-8 col-lg-9">
                <x-state-select :guid="$guid" :countryId="$adres->country_id" :stateId="$adres->state_id"/>
            </div>
        </div>

        <div class="row g-3 g-xl-4 mb-3 align-items-center">
            <label class="col-md-4 col-lg-3 fw-semibold">{{__('İlçe')}}</label>
            <div class="col-md-8 col-lg-9">
                <input type="text" class="form-control" name="county" value="{{old('county', $adres->county)}}" autocomplete="city" required/>
            </div>
        </div>

        <div class="row g-3 g-xl-4 align-items-center">
            <label class="col-md-4 col-lg-3 fw-semibold">{{__('Adres')}}</label>
            <div class="col-md-8 col-lg-9">
                <textarea class="form-control" name="address" required>{{old('address',$adres->address)}}</textarea>
            </div>
        </div>

        <hr class="my-4">

        <div class="row g-3 g-xl-4 align-items-center">
            <label class="col-md-4 col-lg-3 fw-semibold"></label>
            <div class="col-md-8 col-lg-9">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="use-for-billing-{{$adres->id}}" name="use_for_billing" onchange="useForBilling(this);" @checked(old('use_for_billing', $adres->use_for_billing))>
                    <label class="form-check-label" for="use-for-billing-{{$adres->id}}">{{__('Bu adresi fatura bilgilerimde de kullan')}}</label>
                </div>
            </div>
        </div>
        
        <div @class(['for-billing' => $adres->use_for_billing,'for-billing d-none' => !$adres->use_for_billing])>
            <hr class="my-4">
            <input type="hidden" name="billing_type" value="Kurumsal">

            <div @class(['row g-3 g-xl-4 align-items-center billing-details-kurumsal'])>

                <div class="col-12">
                    <div class="row g-3 g-xl-4 mb-3 align-items-center">
                        <label class="col-md-4 col-lg-3 fw-semibold">{{__('Firma Adı')}}</label>
                        <div class="col-md-8 col-lg-9">
                            <input type="text" class="form-control" name="company_name" value="{{old('company_name',$adres->company_name)}}" {{$adres->billing_type == 'Kurumsal' ? 'required' : ''}} autocomplete="company_name"/>
                        </div>
                    </div>
                    <div class="row g-3 g-xl-4 mb-3 align-items-center">
                        <label class="col-md-4 col-lg-3 fw-semibold">{{__('Vergi Dairesi')}}</label>
                        <div class="col-md-8 col-lg-9">
                            <input type="text" class="form-control" name="vd" value="{{old('vd',$adres->vd)}}" {{$adres->billing_type == 'Kurumsal' ? 'required' : ''}} autocomplete="vd"/>
                        </div>
                    </div>
                    <div class="row g-3 g-xl-4 mb-3 align-items-center">
                        <label class="col-md-4 col-lg-3 fw-semibold">{{__('Vergi no')}}</label>
                        <div class="col-md-8 col-lg-9">
                            <input type="text" class="form-control" name="vkn" value="{{old('vkn',$adres->vkn)}}" {{$adres->billing_type == 'Kurumsal' ? 'required' : ''}} autocomplete="vkn"/>
                        </div>
                    </div>
                    <div class="row g-3 g-xl-4 align-items-center">
                        <label class="col-md-4 col-lg-3 fw-semibold"></label>
                        <div class="col-md-8 col-lg-9">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="e-fatura-mukellef-{{$adres->id}}" name="e_fatura" @checked(old('e_fatura', $adres->e_fatura))>
                                <label class="form-check-label" for="e-fatura-mukellef-{{$adres->id}}">{{__('E-fatura mükellefiyim')}}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4">

        <div class="row g-3 g-xl-4 align-items-center justify-content-end">
            <div class="col-md-8 col-lg-9">
                <button class="btn btn-success" type="submit">{{__('Güncelle')}}</button>
            </div>
        </div>

    </form>
</div>

