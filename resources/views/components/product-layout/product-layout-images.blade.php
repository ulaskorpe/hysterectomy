<div class="position-relative mw-500px">
    <div class="swiper rounded" id="product-main-swiper">

        <div class="swiper-wrapper">

            <div class="swiper-slide my-0">
                <a href="{{ $images['mainImage']->getUrl() }}" data-fancybox="product">
                    <img src="{{ $images['mainImage']->getUrl('500x500') }}" alt="{{$name}}" class="img-fluid rounded" width="500" height="500"/>
                </a>
            </div>

            @if ($images['galleryImages'])
            @foreach ($images['galleryImages'] as $image)
            <div class="swiper-slide my-0">
                <a href="{{$image->getUrl()}}" data-fancybox="product">
                    <img loading="lazy" src="{{$image->getUrl('500x500')}}" @class(['img-fluid rounded']) width="500" height="500"/>
                </a>
            </div>
            @endforeach
            @endif

        </div>

        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>

    </div>
</div>