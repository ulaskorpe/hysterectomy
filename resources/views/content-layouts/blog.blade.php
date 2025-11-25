<x-fe-layout :header-layout="$content->header_layout" :bread-crumb="['data' => $content->uri->breadcrumb ?? [],'title' => $content->name]">

    <section @class(['bg-theme-head'])>

        <div class="container py-5">
            <h1 class="h2 mb-0">{{ $content->page_title }}</h1>
            @if ($content->summary)
            <div class="mt-3">
                {!! $content->summary !!}
            </div>
            <div class="mt-4 d-flex align-items-center gap-2">
                <div class="d-flex align-items-center gap-2 fs-sm rounded-pill px-2 py-1 bg-white">
                    <i class="bi bi-calendar-check"></i>
                    <span>{{ $content->start_date->format('d.m.Y') }}</span>
                </div>
                <div class="d-flex align-items-center gap-2 fs-sm rounded-pill px-2 py-1 bg-white">
                    <i class="bi bi-person-circle"></i>
                    <span>{{ $content->user->name ?? '' }}</span>
                </div>
            </div>
            @endif
        </div>

    </section>

    <div class="content-section blog-page">
        <div class="container">
            <div class="row justify-content-center g-4 g-xl-5">
                <div class="col-xl-8">

                @if ($content->content && count($content->content) > 0)
                    @foreach ($content->content[0]['containers'][0]['rows'][0]['columns'][0]['elements'] as $element)
                    <x-column-element :content="$content" :element="$element"/>
                    @endforeach
                @endif

                </div>

                @if ($content->media_objects['mainImage'])
                <div class="col-xl-4">
                    <div class="sticky-top sticky-column">
                        <img src="{{$content->media_objects['mainImage']->original_url}}" class="img-fluid" width="{{$content->media_objects['mainImage']->custom_properties['width']}}" height="{{$content->media_objects['mainImage']->custom_properties['height']}}" alt="{{$content->name}}">
                    </div>
                </div>
                @endif
                
            </div>
        </div>
    </div>

    <x-slot name="headerScripts">
        @if (isset($content->additional['headerScripts']))
        {!! $content->additional['headerScripts'] !!}
        @endif
    </x-slot>

    <x-slot name="footerScripts">

        @if (isset($content->additional['footerScripts']))
        {!! $content->additional['footerScripts'] !!}
        @endif

    </x-slot>

</x-fe-layout>