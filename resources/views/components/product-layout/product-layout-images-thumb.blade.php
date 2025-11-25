<div class="position-relative">
    <div class="swiper rounded" id="product-main-thumb-swiper" thumbsSlider>

        <div class="swiper-wrapper">

            <div class="swiper-slide my-0 d-flex cursor-pointer rounded bg-theme">
                <img src="{{ $images['mainImage']->preview_url }}" alt="{{$name}}" class="w-100 h-100 object-fit-cover rounded" width="200" height="200"/>
            </div>

            @if ($images['galleryImages'])
            @foreach ($images['galleryImages'] as $image)
            <div class="swiper-slide my-0 d-flex cursor-pointer rounded bg-theme">
                <img src="{{ $image->preview_url }}" alt="{{$name}}" class="w-100 h-100 object-fit-cover rounded" width="200" height="200"/>
            </div>
            @endforeach
            @endif

        </div>

    </div>
</div>