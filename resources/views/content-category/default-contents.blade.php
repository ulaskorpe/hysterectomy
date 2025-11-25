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
        
        @foreach ($category_contents as $category_content)
        <a @class([
            'row g-4 g-xl-5 align-items-center text-reset text-decoration-none hover-scale',
            'mb-5 mb-xl-8' => !$loop->last,
            'flex-lg-row-reverse' => $loop->even
        ]) href="{{$category_content->uri->final_uri}}">
            @if ($category_content->media_objects['mainImage'])
            <div class="col-lg-7">
                <img loading="lazy" src="{{$category_content->media_objects['mainImage']->getUrl()}}" class="w-100 h-150px object-fit-cover" width="{{$category_content->media_objects['mainImage']->getCustomProperty('width')}}" height="{{$category_content->media_objects['mainImage']->getCustomProperty('height')}}" alt="{{$category_content->name}}">
            </div>
            @endif

            <div class="col-lg-5 text-center">
                <h3 class="h2 mb-0">{{ $category_content->name }}</h3>
            </div>

        </a>
        @endforeach

    </div>

    @if ($category_contents->total() > 30)
    <div class="mt-5 mt-xl-7">
        {{ $category_contents->links() }}
    </div>
    @endif
    
</section>