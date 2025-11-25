<x-fe-layout :header-layout="$content->header_layout">

    @if (data_get($content->additional, 'hideHeader') === false)
    <x-page-header :content="$content"/>
    @endif
    
    @if ($content->content && count($content->content) > 0)
        @foreach ($content->content as $section)
        <x-section :section="$section" :content="$content"/>
        @endforeach
    @endif

    @php

        $category_contents = \Spatie\QueryBuilder\QueryBuilder::for(\App\Models\Content::class)
        ->with([
            'content_categories',
            'library_media',
            'uri',
            'content_type'
        ])
        ->when(request()->input('search'), function ($query, $search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('name','like','%'.$search.'%')->orWhere('summary','like','%'.$search.'%');
            });
        })
        ->whereHas('content_categories',function($cat) use ($content) {
            $cat->where('id',$content->id);
        })
        ->defaultSort('order_column')
        ->allowedSorts('name','created_at','start_date','order_column')
        ->paginate(30)->withQueryString();
        
    @endphp

    <section @class([
        'content-section',
        'pt-0' => $content->content && count($content->content) > 0
    ])>
        <div class="container">
            
            <div class="row g-4 row-cols-1 row-cols-md-2 row-cols-lg-3">
                @foreach ($category_contents as $category_content)
                <div class="col">
                    <div class="w-100 h-100 d-flex flex-column">
                        <div class="position-relative">
                            @if ($category_content->media_objects['mainImage'])
                            <div class="overflow-hidden hover-image-scale h-150px h-lg-200px">
                                <img loading="lazy" src="{{$category_content->media_objects['mainImage']->getUrl()}}" class="w-100 h-auto object-fit-cover" width="{{$category_content->media_objects['mainImage']->getCustomProperty('width')}}" height="{{$category_content->media_objects['mainImage']->getCustomProperty('height')}}" alt="{{$category_content->name}}">
                            </div>
                            @else
                            <div class="bg-light d-flex align-items-center justify-content-center h-150px">
                                <i class="bi bi-image fs-3"></i>
                            </div>
                            @endif
                            @if ($category_content->content_categories->count() > 0)
                            <div class="d-flex flex-wrap justify-content-end gap-2 position-absolute start-0 bottom-0 w-100 p-3 p-xl-4">
                                @foreach ($category_content->content_categories as $c_category)
                                @if ($c_category->parent_id)
                                    <a class="btn btn-sm btn-secondary py-1 px-3 fs-xs" href="{{$c_category->uri->final_uri}}">{{ $c_category->name }}</a>
                                @endif
                                @endforeach
                            </div>
                            @endif
                        </div>
                        <div class="bg-gray-100 d-flex flex-column gap-3 p-3 p-xl-4">
                            <small class="text-muted">
                                <i class="bi bi-calendar me-2"></i>{{$category_content->start_date->format('d.m.Y')}}
                            </small>
                            <h2 class="h5 mb-0">{{ $category_content->name }}</h2>
                            @if($category_content->summary)
                            <p class="fs-sm mb-0">{{ Str::limit(strip_tags($category_content->summary),100,'...') }}</p>
                            @endif
                            <div class="d-flex mt-auto pt-2">
                                <a href="{{$category_content->uri->final_uri}}" class="btn btn-sm btn-primary py-1 d-flex align-items-center gap-2">
                                    {{ __('Read the Blog Post') }}
                                    <i class="bi bi-arrow-up-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

        </div>

        @if ($category_contents->total() > 30)
        <div class="mt-5 mt-xl-7">
            {{ $category_contents->links() }}
        </div>
        @endif
        
    </section>

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