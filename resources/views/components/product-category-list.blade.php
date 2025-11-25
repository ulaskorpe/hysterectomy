@php

    $categories = \Spatie\QueryBuilder\QueryBuilder::for(\App\Models\ProductCategory::class)
    ->with('uri')
    ->with('product_type:id,name')
    ->with(['childs' => function($c){
        $c->with('uri');
    }])
    ->whereNull('parent_id')
    ->when($segment,function($query) use ($segment) {
        $query->whereHas('product_type',function($qq) use ($segment) {
            $qq->where('product_segment_id',$segment->id);
        });
    })
    ->when($productType,function($query) use ($productType) {
        $query->where('product_type_id',$productType->id);
    })
    ->get();

@endphp

@foreach ($categories as $key => $category)
    
<div class="bg-cover bg-center d-flex min-h-400px align-items-center position-relative mb-3" >

    @if ($category->media_objects['headerImage'])
    <div class="parallax" data-parallax-image="{{ $category->media_objects['headerImage']['original_url'] }}"></div>
    @endif
    
    <div class="overlay bg-dark opacity-60"></div>
    <div class="container-fluid px-xxl-7 position-relative text-white">
        <div class="row g-3 g-xl-5">
            <div class="col-lg-8">
                @if ($segment)<small class="fs-6 mb-1">{{$category->product_type->name}}</small>@endif
                <h2 class="mb-4 title-responsive">{{$category->name}}</h2>
                @if($category->summary)
                <p class="p-responsive mb-4">{{ Str::limit(strip_tags($category->summary),200,'...') }}</p>
                @endif
                <a href="{{$category->uri->final_uri}}" class="btn btn-secondary">Detaylı Bilgi</a>
            </div>
            @if ($category->childs->count() > 0)
            <div @class(['col-lg-4', 'border-start' => !$isMobile])>
                <ul class="list-unstyled mb-0">
                    @foreach ($category->childs as $child)
                    <li class="hstack gap-2 align-items-center">
                        <i class="bi bi-arrow-right fs-4"></i>
                        <a href="{{$child->uri->final_uri}}" class="p-responsive link-light link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover">{{ $child->name }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>
</div>

@endforeach