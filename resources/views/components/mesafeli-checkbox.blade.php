@php

$sale_contract_page = null;
$on_bilgilendirme_page = null;
$kvkk_page = null;

if($settings->sale_contract_page){
    $sale_contract_page = \App\Models\Content::where('uuid',$settings->sale_contract_page)->first();
}

if($settings->kvkk_page){
    $kvkk_page = \App\Models\Content::where('uuid',$settings->kvkk_page)->first();
}

//odeme teslimat on bilgilendirme icin kullandim
if($settings->payment_delivery_page){
    $on_bilgilendirme_page = \App\Models\Content::where('uuid',$settings->payment_delivery_page)->first();
}

@endphp

@if ($on_bilgilendirme_page)

    <div class="form-check form-switch position-relative">
        <input class="form-check-input" type="checkbox" role="switch" id="onbilgilendirme-switch" required name="onbilgilendirme" @checked(old('onbilgilendirme'))>
        <label class="form-check-label fs-sm cursor-pointer" for="onbilgilendirme-switch">{{__('Ön bilgilendirme formunu okudum, onaylıyorum.')}}</label>
        <div class="invalid-feedback">{{ __('Ön bilgilendirme formunu onaylayınız...') }}</div>
        <div class="click-area position-absolute cursor-pointer start-0 top-0 w-100 h-100 zindex-3" data-bs-toggle="offcanvas" data-bs-target="#offcanvas-onbilgilendirme"></div>
    </div>

    @push('footer')
    <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="offcanvas-onbilgilendirme">
        <div class="offcanvas-header">
            <span class="h4 offcanvas-title">{{$on_bilgilendirme_page->name}}</span>
            <button type="button" class="btn btn-white btn-link p-0" data-bs-dismiss="offcanvas" aria-label="Close"><i class="bi bi-x-lg fs-4"></i></button>
        </div>
        <div class="offcanvas-body" id="onbilgilendirme-body">
            {!! $on_bilgilendirme_page->description !!}
        </div>
        <div class="offcanvas-footer p-4 border-top">
            <button type="button" class="btn btn-success" disabled id="onbilgilendirme-button" data-bs-dismiss="offcanvas">{{__('Okudum, onaylıyorum')}}</button>
        </div>
    </div>

    <script>

        const onbilgilendirmeSwitch = document.getElementById('onbilgilendirme-switch');
        const onbilgilendirmeOffCanvas = document.getElementById('offcanvas-onbilgilendirme');
        const onbilgilendirmeBody = document.getElementById('onbilgilendirme-body');
        const onbilgilendirmeButton = document.getElementById('onbilgilendirme-button');

        onbilgilendirmeOffCanvas.addEventListener('show.bs.offcanvas', event => {
            onbilgilendirmeSwitch.checked = false;
        });

        onbilgilendirmeBody.addEventListener('scroll', function () {
            const scrollTop = onbilgilendirmeBody.scrollTop;
            const scrollHeight = onbilgilendirmeBody.scrollHeight;
            const clientHeight = onbilgilendirmeBody.clientHeight;

            const isBottom = scrollTop + clientHeight >= scrollHeight - 5;

            if (isBottom) {
                onbilgilendirmeButton.disabled = false;
            } else {
                onbilgilendirmeButton.disabled = true;
            }
        });

        onbilgilendirmeButton.addEventListener('click',() => {
            onbilgilendirmeSwitch.checked = true;
        });

    </script>

    @endpush

@endif

@if ($sale_contract_page)

    <div class="form-check form-switch position-relative">
        <input class="form-check-input" type="checkbox" role="switch" id="mesafeli-satis-switch" required name="mesafeli" @checked(old('mesafeli'))>
        <label class="form-check-label fs-sm cursor-pointer" for="mesafeli-satis-switch">{{__('Mesafeli satış sözleşmesini okudum, onaylıyorum.')}}</label>
        <div class="invalid-feedback">{{ __('Satış sözleşmesini onaylayınız...') }}</div>
        <div class="click-area position-absolute cursor-pointer start-0 top-0 w-100 h-100 zindex-3" data-bs-toggle="offcanvas" data-bs-target="#offcanvas-mesafeli"></div>
    </div>

    @push('footer')
    <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="offcanvas-mesafeli">
        <div class="offcanvas-header">
            <span class="h4 offcanvas-title">{{$sale_contract_page->name}}</span>
            <button type="button" class="btn btn-white btn-link p-0" data-bs-dismiss="offcanvas" aria-label="Close"><i class="bi bi-x-lg fs-4"></i></button>
        </div>
        <div class="offcanvas-body" id="mesafeli-body">
            {!! $sale_contract_page->description !!}
        </div>
        <div class="offcanvas-footer p-4 border-top">
            <button type="button" class="btn btn-success" disabled id="mesafeli-button" data-bs-dismiss="offcanvas">{{__('Okudum, onaylıyorum')}}</button>
        </div>
    </div>

    <script>

        const mesafeliSwitch = document.getElementById('mesafeli-satis-switch');
        const mesafeliOffCanvas = document.getElementById('offcanvas-mesafeli');
        const mesafeliBody = document.getElementById('mesafeli-body');
        const mesafeliButton = document.getElementById('mesafeli-button');

        mesafeliOffCanvas.addEventListener('show.bs.offcanvas', event => {
            mesafeliSwitch.checked = false;
        });

        mesafeliBody.addEventListener('scroll', function () {
            const scrollTop = mesafeliBody.scrollTop;
            const scrollHeight = mesafeliBody.scrollHeight;
            const clientHeight = mesafeliBody.clientHeight;

            const isBottom = scrollTop + clientHeight >= scrollHeight - 5;

            if (isBottom) {
                mesafeliButton.disabled = false;
            } else {
                mesafeliButton.disabled = true;
            }
        });

        mesafeliButton.addEventListener('click',() => {
            mesafeliSwitch.checked = true;
        });

    </script>

    @endpush

@endif

@if ($kvkk_page)

    <div class="form-check form-switch position-relative">
        <input class="form-check-input" type="checkbox" role="switch" id="kvkk-switch" required name="kvkk" @checked(old('kvkk'))>
        <label class="form-check-label fs-sm cursor-pointer" for="kvkk-switch">{{__('Aydınlatma metnini okudum, onaylıyorum.')}}</label>
        <div class="invalid-feedback">{{ __('Aydınlatma metnini onaylayınız...') }}</div>
        <div class="click-area position-absolute cursor-pointer start-0 top-0 w-100 h-100 zindex-3" data-bs-toggle="offcanvas" data-bs-target="#offcanvas-kvkk"></div>
    </div>

    @push('footer')
    <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="offcanvas-kvkk">
        <div class="offcanvas-header">
            <span class="h4 offcanvas-title">{{$kvkk_page->name}}</span>
            <button type="button" class="btn btn-white btn-link p-0" data-bs-dismiss="offcanvas" aria-label="Close"><i class="bi bi-x-lg fs-4"></i></button>
        </div>
        <div class="offcanvas-body" id="kvkk-body">
            {!! $kvkk_page->description !!}
        </div>
        <div class="offcanvas-footer p-4 border-top">
            <button type="button" class="btn btn-success" disabled id="kvkk-button" data-bs-dismiss="offcanvas">{{__('Okudum, onaylıyorum')}}</button>
        </div>
    </div>

    <script>

        const kvkkSwitch = document.getElementById('kvkk-switch');
        const kvkkOffCanvas = document.getElementById('offcanvas-kvkk');
        const kvkkBody = document.getElementById('kvkk-body');
        const kvkkButton = document.getElementById('kvkk-button');

        kvkkOffCanvas.addEventListener('show.bs.offcanvas', event => {
            kvkkSwitch.checked = false;
        });

        kvkkBody.addEventListener('scroll', function () {
            const scrollTop = kvkkBody.scrollTop;
            const scrollHeight = kvkkBody.scrollHeight;
            const clientHeight = kvkkBody.clientHeight;

            const isBottom = scrollTop + clientHeight >= scrollHeight - 5;

            if (isBottom) {
                kvkkButton.disabled = false;
            } else {
                kvkkButton.disabled = true;
            }
        });

        kvkkButton.addEventListener('click',() => {
            kvkkSwitch.checked = true;
        });

    </script>

    @endpush

@endif