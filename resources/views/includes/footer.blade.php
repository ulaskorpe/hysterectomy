@php
    $logo = isset($settings->logo['main']) ? $settings->logo['main'] : null;
@endphp

<footer class="bg-theme-3 text-white">

    <div class="container large-container py-5 py-xl-7">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-lg-between gap-4 gap-xl-5 w-100">
            @if ($logo)
            <div class="text-center text-lg-start">
                <img class="img-fluid" fetchpriority="high" src="{{ $logo['original_url'] }}" alt="{{ $settings->site_name }}" width="{{ $logo['custom_properties']['width'] }}" height="{{ $logo['custom_properties']['height'] }}">
            </div>
            @endif
            <div class="d-flex flex-column align-items-center text-center gap-2">
                <x-social-media-accounts classes="justify-content-center text-white" />
                @if ($settings->contact['phone'])
                <div class="d-flex align-items-center gap-2 text-light">
                    <i class="bi bi-telephone-fill rotate-270"></i>
                    <span class="lh-1">{{ $settings->contact['phone'] }}</span>
                </div>
                @endif
            </div>
            @if ($settings->contact['address'])
            <div class="text-center text-lg-end">
                {!! $settings->contact['address'] !!}
            </div>
            @endif
        </div>
    </div>

</footer>