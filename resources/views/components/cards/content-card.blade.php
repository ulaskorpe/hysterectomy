@if ($content->uri)
<a href="{{$content->uri->final_uri}}" class="w-100 h-100 content-card d-flex flex-column gap-3 text-reset text-decoration-none text-start">
@else
<div class="w-100 h-100 content-card d-flex flex-column gap-3">
@endif

    @if ($content->media_objects['mainImage'])
    <div class="overflow-hidden hover-image-scale h-150px h-lg-200px h-xl-250px">
        <img loading="lazy" src="{{$content->media_objects['mainImage']->getUrl()}}" class="w-100 h-auto object-fit-cover" width="{{$content->media_objects['mainImage']->getCustomProperty('width')}}" height="{{$content->media_objects['mainImage']->getCustomProperty('height')}}" alt="{{$content->name}}">
    </div>
    @else
    <div class="bg-light d-flex align-items-center justify-content-center h-150px">
        <i class="bi bi-image fs-3"></i>
    </div>
    @endif
    @if ($useDate)
        <small class="text-muted">
            <i class="bi bi-calendar me-2"></i>{{$content->start_date->format('d.m.Y')}}
        </small>
    @endif
    <h3 class="p-responsive mb-0">
        {{$content->name}}
    </h3>
    @if($content->summary)
    <p class="fs-sm mb-0">{{ Str::limit(strip_tags($content->summary),100,'...') }}</p>
    @endif

    <div class="d-flex">
        <div class="btn btn-sm btn-primary d-flex alig-items-center gap-2 py-1">{{ __('Read more') }}<i class="bi bi-arrow-up-right"></i></div>
    </div>

@if ($content->uri)
</a>
@else
</div>
@endif