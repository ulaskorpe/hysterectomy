@php
    $offCanvasId = 'canvas-'.Str::uuid();
@endphp

<div class="content-section">
    @if ($isMobile)
    <div class="container mb-4">
        <div class="d-flex align-items-center gap-2 pb-4 border-bottom">
            <button class="btn btn-light btn-sm d-flex align-items-center border" type="button" data-bs-toggle="offcanvas" data-bs-target="#{{$offCanvasId}}" aria-controls="{{$offCanvasId}}">
                <i class="bi bi-filter"></i>
                <span class="ms-2">{{ __('FİLTRELER') }}</span>
            </button>
            @if (request()->has('filter'))
            <a href="{{ request()->url()}}" onclick="FreezeUI({text:' '})" data-bs-toggle="tooltip" title="Tüm Filtreleri Kaldır" class="alert alert-danger mb-0 py-1 px-2">
                <i class="bi bi-x-lg"></i>
            </a>
            @endif
        </div>           
    </div>
    @endif

    <div class="container">
        <div class="row g-4 g-xl-5">
            @if (!$isMobile)
            <div class="col-lg-3">
                <x-product-filters :useActiveFilters="false"/>
            </div> 
            @endif
            <div @class(['col-12' => $isMobile,'col-lg-9' => !$isMobile])>
                <x-product-list :column-count="2"/>
            </div>
        </div>
    </div>
</div>

@if ($isMobile)
@push('footer')
<div class="offcanvas offcanvas-start border-0" tabindex="-1" id="{{$offCanvasId}}">
    <div class="offcanvas-header bg-theme border-0 text-white">
        <span class="offcanvas-title" id="{{$offCanvasId}}-title">{{ __('Ürünleri Filtrele') }}</span>
        <button type="button" class="btn btn-bg-primary text-white px-2 py-0" data-bs-dismiss="offcanvas" aria-label="Close"><i class="bi bi-x fs-4"></i></button>
    </div>
    <div class="offcanvas-body" id="{{$offCanvasId}}-body">
        <x-product-filters :useActiveFilters="false"/>
    </div>
</div>
@endpush
@endif