<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">

        {{ seo()->render() }}

		<meta name="author" content="{{config('app.name')}}">
		<meta name="robots" content="index, follow, noodp, noydir">
		<meta content="width=device-width, initial-scale=1, maximum-scale=5" name="viewport">
		<meta name="format-detection" content="telephone=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @if (isset($settings->logo['favicon']['original_url']))
        <link href="{{ $settings->logo['favicon']['original_url'] }}" rel="shortcut icon">
        @endif

        {{--Bu canonical isini seo tool da cozebiliyor muyuz bakalim--}}
        <link rel="canonical" href="{{ request()->url() }}">

        @if (isset($connecteds) && $connecteds->count() > 0)
        @foreach ($connecteds as $connected)
        @if ( $connected->uuid == $settings->home_page )
        @if ($connected['language'] == config('languages.default'))
        <link rel="alternate" href="{{ config('app.url') }}" hreflang="{{$connected->language}}">
        @else
        <link rel="alternate" href="{{ config('app.url') .'/'. $connected->language }}" hreflang="{{$connected->language}}">
        @endif
        @else
        <link rel="alternate" href="{{ $connected->uri->final_uri }}" hreflang="{{$connected->language}}">
        @endif
        @endforeach

        @endif

        <link rel="preload" href="/fe/css/fonts/subset-Manrope-ExtraBold.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="/fe/css/fonts/subset-Manrope-Bold.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="/fe/css/fonts/subset-Manrope-Light.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="/fe/css/fonts/subset-Manrope-Regular.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="/fe/css/fonts/subset-Manrope-SemiBold.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="/fe/css/fonts/subset-Manrope-Medium.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="/fe/css/fonts/bootstrap-icons.woff2?24e3eb84d0bcaf83d77f904c78ac1f47" as="font" type="font/woff2" crossorigin>

        <link rel="preload" href="{{ mix('/fe/css/main.css') }}" as="style">
        <link rel="preload" href="{{ mix('/fe/css/vendors.css') }}" as="style">

        @stack('preload')

        <link rel="stylesheet" href="{{ mix('/fe/css/main.css') }}">

        {!! $settings->scripts['header'] ?? '' !!}

        {{ $headerScripts ?? '' }}

        @if ($message = Session::get('form_after_fields'))
		<script id="form-submit-fields">
            const formSubmitFields = @json($message);
        </script>
		@endif
        
        @if ($message = Session::get('formAfterSubmit'))
        {!! $message !!}
        @endif

        @stack('head')
        
    </head>
    <body @class([
        'd-flex flex-column overflow-x-hidden top-0',
        'body-padding' => !$isMobile,
        'body-padding-mobile' => $isMobile,
        'rtl-mode' => app()->getLocale() == 'ar'])>

        {!! $settings->scripts['afterBody'] ?? '' !!}

        {{ $afterBodyScripts ?? '' }}

        @include('includes/navbar',['content' => $content, 'isHome' => $isHome, 'headerLayout' => $headerLayout ?? null])

        {{ $pageHeader ?? '' }}

        <x-flash-message />

        <main>
            {{ $slot }}
        </main>

        <div class="mt-auto">
            @if (isset($settings->footer_layout[app()->getLocale()]) && count($settings->footer_layout[app()->getLocale()]['content']) > 0)
                @foreach ($settings->footer_layout[app()->getLocale()]['content'] as $section)
                <x-section :section="$section"/>
                @endforeach
            @endif
            @include('includes/footer')
        </div>

        <div class="offcanvas offcanvas-size-xxxl offcanvas-end border-0" tabindex="-1" id="dynamic-offcanvas">
            <div class="offcanvas-header lead-responsive fw-semibold border-0 bg-primary text-white py-3 px-4">
                <span class="offcanvas-title" id="dynamic-offcanvas-title"></span>
                <button type="button" class="btn box-30 p-0 d-flex align-items-center justify-content-center rounded-circle bg-white text-primary ms-auto" data-bs-dismiss="offcanvas" aria-label="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="offcanvas-body px-0 py-4" id="dynamic-offcanvas-body">
                <div class="ps-4">
                    <div class="spinner-border spinner-border-lg" role="status"><span class="visually-hidden">Loading...</span></div>
                </div>
            </div>
        </div>

        @if (config('app-ea.app_ecommerce_active'))
        <div class="offcanvas offcanvas-size-xl offcanvas-end border-0" tabindex="-1" id="cart-mini">
            <div class="offcanvas-header lead-responsive fw-semibold border-0 bg-primary text-white py-3 px-4">
                <span class="offcanvas-title">{{__('Sepetim')}}</span>
                <button type="button" class="btn box-30 p-0 d-flex align-items-center justify-content-center rounded-circle bg-white text-primary ms-auto" data-bs-dismiss="offcanvas" aria-label="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="offcanvas-body p-4" id="mini-cart-body">
                <x-cart-mini />
            </div>
            @if (Jackiedo\Cart\Facades\Cart::getDetails()->get('items_count') > 0)
            <div class="offcanvas-footer p-0 d-flex">
                <a href="{{route('cart.index')}}" class="btn btn-light btn-lg w-50 rounded-0">{{ __('Sepete Git') }}</a>
                <a href="{{route('payment.index')}}" class="btn btn-primary btn-lg ms-auto w-50 rounded-0">{{ __('Ödeme') }}</a>
            </div>
            @endif
        </div>
        @endif

        {{-- <x-whatsapp-widget /> --}}

        @include('cookie-consent::index')

        {{ $footerScriptsSource ?? '' }}

        <link rel="stylesheet" href="{{ mix('/fe/css/vendors.css') }}">
        <script src="{{ asset('/fe/js/vendors.js') }}" defer></script>
        <script src="{{ asset('/fe/js/main.js') }}" defer></script>

        @if (config('app-ea.app_ecommerce_active'))
        <script src="{{ asset('/fe/js/ecommerce.js') }}" defer></script>   
        @endif

        {!! $settings->scripts['footer'] ?? '' !!}

        {{ $footerScripts ?? '' }}

        @stack('footer')

    </body>
</html>