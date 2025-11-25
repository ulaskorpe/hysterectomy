@php
    
    $headerVideo = $content->media_objects['mainVideo'];
    $title = $content->page_title;
    $headerImage = $content->media_objects['headerImage'];
    $subTitle = $content->additional['subTitle'] ?? "";
    $buttonText = $content->additional['buttonText'] ?? "";
    $buttonLink = $content->additional['buttonLink'] ?? "";
    $contentAttributes = $content->attributes ?? [];
    $breadCrumb = ['data' => $content->uri->breadcrumb ?? [],'title' => $content->name];

@endphp

<section @class([
    'bg-theme-head'
])>

    @if ($headerVideo)
        <video class="bg-video" autoplay muted loop playsinline poster="{{ $headerImage ? $headerImage->getUrl() : '' }}">
            <source src="{{$headerVideo->original_url}}" type="video/mp4">
        </video>
        <div class="overlay bg-dark opacity-30"></div>
    @endif

    <div class="container large-container">
        <div @class(['row g-0'])>
            
            <div @class(['col-lg-7 py-5 d-flex align-items-center'])>
                <div class="d-flex flex-column gap-3">
                    <h1 class="mb-0">{!!$title!!}</h1>
                    @if ($subTitle)
                    <p class="fs-lg mb-0">{!!$subTitle!!}</p>
                    @endif
                </div>
            </div>

            @if($headerImage)
            <div class="col-12 col-lg-5">
                <div class="d-flex position-relative h-100 align-items-end">
                    <img fetchpriority="high" class="position-relative img-fluid mx-auto" src="{{$headerImage->getUrl()}}" alt="{{$content->name}}" width="{{$headerImage->getCustomProperty('width')}}" height="{{$headerImage->getCustomProperty('height')}}">
                </div>
            </div>
            @endif
        </div>
    </div>

</section>

@if($headerImage)
@push('preload')
<link rel="preload" as="image" href="{{$headerImage->getUrl()}}" fetchpriority="high">
@endpush
@endif

{{--
@if ($breadCrumb)
<div class="bg-light border-bottom">
    <div class="container py-3">
        <x-breadcrumb :data="$breadCrumb['data']" :title="$breadCrumb['title']" />
    </div>
</div>
@endif
--}}