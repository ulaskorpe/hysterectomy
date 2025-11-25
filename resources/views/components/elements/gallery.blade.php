@props([
    'element' => null
])

@if ($element)

    @php

        $elemData = $element['data'];
        $elemDivClasses = [];

        if($elemData['baseDesignOptions']['class']) $elemDivClasses[] = $elemData['baseDesignOptions']['class'];
        if(isset($elemData['baseDesignOptions']['position'])) $elemDivClasses[] = $elemData['baseDesignOptions']['position'];

        foreach ($elemData['baseDesignOptions']['margin'] as $key => $value) {
            if($value) $elemDivClasses[] = $value;
        }
        foreach ($elemData['baseDesignOptions']['padding'] as $key => $value) {
            if($value) $elemDivClasses[] = $value;
        }

        if($elemData['type'] == 'carousel'){
            $elemDivClasses[] = $elemData['navigationPosition'];
        }

        $galleryId = 'gallery-'.rand();

    @endphp

    <div @class($elemDivClasses)>

        @switch($elemData['type'])

            @case("carousel")

                @php

                    $swiperOptions = [
                        'watchSlidesProgress' => true,
                        'lazyPreloadPrevNext' => 0,
                        'freeMode' => $elemData['slidesPerViewAuto'] ? false : true,
                        'mousewheel' => $elemData['slidesPerViewAuto'] ? true : false,
                        'pagination' => [
                            'el' => '.swiper-pagination',
                            'clickable' => true,
                            'dynamicBullets' => true
                        ],
                        'autoplay' => [
                            'delay' => 7000,
                            'disableOnInteraction' => true
                        ],
                        'grabCursor' => true,
                        'slidesPerView' => $elemData['slidesPerViewAuto'] ? 'auto' : $elemData['columnCounts']['sm'],
                        'spaceBetween' => $elemData['spaceBetween'],
                        'navigation' => [
                            'nextEl' => '.swiper-button-next',
                            'prevEl' => '.swiper-button-prev'
                        ],
                        'grid' => $elemData['rowCounts']['sm'],
                        'breakpoints' => [
                            400 => [
                                'slidesPerView' => $elemData['slidesPerViewAuto'] ? 'auto' : $elemData['columnCounts']['sm'],
                                'grid' => $elemData['rowCounts']['sm'],
                            ],
                            768 => [
                                'slidesPerView' => $elemData['slidesPerViewAuto'] ? 'auto' : $elemData['columnCounts']['md'],
                                'grid' => $elemData['rowCounts']['md'],
                            ],
                            998 => [
                                'slidesPerView' => $elemData['slidesPerViewAuto'] ? 'auto' : $elemData['columnCounts']['lg'],
                                'grid' => $elemData['rowCounts']['lg'],
                            ],
                            1200 => [
                                'slidesPerView' => $elemData['slidesPerViewAuto'] ? 'auto' : $elemData['columnCounts']['xl'],
                                'grid' => $elemData['rowCounts']['xl'],
                            ]
                        ]
                    ];

                    $jsonOptions = json_encode($swiperOptions);
                @endphp
                
                <div @class([
                    'swiper ea-swiper opacity-100',
                    'pb-5' => $elemData['pagination'],
                    'theme-navigation' => $elemData['navigation'] && $elemData['navigationPosition'] == 'navigation-default'
                ]) id="{{ $elemData['swiperId'] ?? 'swiper-'.rand() }}" data-swiper-options="{{$jsonOptions}}">
                    @if ($elemData['navigation'] && $elemData['navigationPosition'] == 'navigation-top-right')
                    <div class="swiper-navigation-div container">
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    </div>
                    @endif
                    <div @class(['swiper-wrapper'])>

                        @foreach ($elemData['medias'] as $media)
                        @php
                            $src = $media['original_url'];
                            $width = $media['custom_properties']['width'];
                            $height = $media['custom_properties']['height'];

                            if(isset($elemData['conversion']) && !empty($elemData['conversion'])){
                                $src = $media['conversion_urls'][$elemData['conversion']];
                                if ($elemData['conversion'] == 'op_url') {
                                    $width = $media['custom_properties']['op_width'];
                                    $height = $media['custom_properties']['op_height'];
                                } else if ($elemData['conversion'] == 'th_url') {
                                    $width = $media['custom_properties']['th_width'];
                                    $height = $media['custom_properties']['th_height'];
                                } else {
                                    $dimArr = explode('x',str_replace('_url','',$elemData['conversion']));
                                    $width = $dimArr[0];
                                    $height = $dimArr[1];
                                }
                            }

                        @endphp
                        <div @class([
                            'swiper-slide',
                            'border' => $elemData['slidesBordered'],
                            'overflow-hidden rounded' => $elemData['slidesBordered'],
                            'shadow' => $elemData['slidesShadow'],
                            'w-auto' => $elemData['slidesPerViewAuto'],
                            $elemData['slidesMobileHeight'] => $elemData['slidesPerViewAuto'] && $elemData['slidesHeight'],
                            $elemData['slidesHeight'] => $elemData['slidesPerViewAuto'] && $elemData['slidesHeight'],
                        ])>
                            @if ($elemData['lightbox'])
                            <a href="{{ $media['original_url'] }}" data-fancybox="{{$galleryId}}">  
                            @endif
                            <img src="{{ $src }}" loading="lazy" alt="{{ $media['name'] }}" width="{{$width}}" height="{{$height}}" @class([
                                'h-100 w-auto mw-100 object-fit-cover',
                                'rounded' => $elemData['imgRounded'],
                                'shadow' => $elemData['imgShadow'],
                            ])>
                            @if ($elemData['lightbox'])
                            </a>
                            @endif
                        </div>
                        @endforeach

                    </div>
                    @if ($elemData['navigation'] && $elemData['navigationPosition'] == 'navigation-default')
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                    @endif
                    @if ($elemData['navigation'] && $elemData['navigationPosition'] == 'navigation-bottom-right')
                    <div class="swiper-navigation-div container">
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    </div>
                    @endif
                    @if ($elemData['pagination'])
                    <div class="swiper-pagination"></div>
                    @endif
                </div>

            @break

            @case("infinitie-loop")

                @php

                    $swiperOptions = [
                        'watchSlidesProgress' => true,
                        'a11y' => true,
                        'freeMode' => true,
                        'loop' => true,
                        'speed' => 9000,
                        'autoplay' => [
                            'delay' => 0.5,
                            'disableOnInteraction' => false,
                            'reverseDirection' => $elemData['reverseDirection']
                        ],
                        'grabCursor' => true,
                        'spaceBetween' => 0,
                        'slidesPerView' => 'auto'
                    ];

                    $jsonOptions = json_encode($swiperOptions);
                @endphp
                
                <div @class([
                    'swiper ea-swiper opacity-100',
                    'pb-5' => $elemData['pagination'],
                    'theme-navigation' => $elemData['navigation'] && $elemData['navigationPosition'] == 'navigation-default'
                ]) id="{{ $elemData['swiperId'] ?? 'swiper-'.rand() }}" data-swiper-options="{{$jsonOptions}}">

                    <div @class(['swiper-wrapper','infinitie-free-swiper'])>

                        @foreach ($elemData['medias'] as $media)
                        @php
                            $src = $media['original_url'];
                            $width = $media['custom_properties']['width'];
                            $height = $media['custom_properties']['height'];

                            if(isset($elemData['conversion']) && !empty($elemData['conversion'])){
                                $src = $media['conversion_urls'][$elemData['conversion']];
                                if ($elemData['conversion'] == 'op_url') {
                                    $width = $media['custom_properties']['op_width'];
                                    $height = $media['custom_properties']['op_height'];
                                } else if ($elemData['conversion'] == 'th_url') {
                                    $width = $media['custom_properties']['th_width'];
                                    $height = $media['custom_properties']['th_height'];
                                } else {
                                    $dimArr = explode('x',str_replace('_url','',$elemData['conversion']));
                                    $width = $dimArr[0];
                                    $height = $dimArr[1];
                                }
                            }

                        @endphp
                        <div @class([
                            'swiper-slide',
                            'border' => $elemData['slidesBordered'],
                            'overflow-hidden rounded' => $elemData['slidesBordered'],
                            'shadow' => $elemData['slidesShadow'],
                            'w-auto' => $elemData['slidesPerViewAuto'],
                            $elemData['slidesMobileHeight'] => $elemData['slidesPerViewAuto'] && $elemData['slidesHeight'],
                            $elemData['slidesHeight'] => $elemData['slidesPerViewAuto'] && $elemData['slidesHeight'],
                        ])>
                            @if ($elemData['lightbox'])
                            <a href="{{ $media['original_url'] }}" data-fancybox="{{$galleryId}}">  
                            @endif
                            <img src="{{ $src }}" loading="lazy" alt="{{ $media['name'] }}" width="{{$width}}" height="{{$height}}" @class([
                                'h-100 w-auto mw-100 object-fit-cover',
                                'rounded' => $elemData['imgRounded'],
                                'shadow' => $elemData['imgShadow'],
                            ])>
                            @if ($elemData['lightbox'])
                            </a>
                            @endif
                        </div>
                        @endforeach

                    </div>
                </div>

            @break

            @case('cloud')

                <div class="cloud-gallery justify-content-between">
                    <div class="arrow left cursor-pointer"><i class="bi bi-chevron-left fs-3"></i></div>
                    <div class="images-area">
                        @foreach ($elemData['medias'] as $media)
                            @php
                                $src = $media['original_url'];
                                $width = $media['custom_properties']['width'];
                                $height = $media['custom_properties']['height'];
                                if(isset($elemData['conversion']) && !empty($elemData['conversion'])){
                                    $src = $media['conversion_urls'][$elemData['conversion']];
                                    if ($elemData['conversion'] == 'op_url') {
                                        $width = $media['custom_properties']['op_width'];
                                        $height = $media['custom_properties']['op_height'];
                                    } else if ($elemData['conversion'] == 'th_url') {
                                        $width = $media['custom_properties']['th_width'];
                                        $height = $media['custom_properties']['th_height'];
                                    } else {
                                        $dimArr = explode('x',str_replace('_url','',$elemData['conversion']));
                                        $width = $dimArr[0];
                                        $height = $dimArr[1];
                                    }
                                }
                            @endphp
                            <img @class([
                                'active' => $loop->first
                            ]) src="{{ $src }}" loading="lazy" alt="{{ $media['name'] }}" width="{{$width}}" height="{{$height}}">
                        @endforeach
                    </div>
                    <div class="arrow right cursor-pointer text-end"><i class="bi bi-chevron-right fs-3"></i></div>
                </div>

            @break

            @default

            <div @class([
                'row g-3',
                'row-cols-'.$elemData['columnCounts']['sm'],
                'row-cols-md-'.$elemData['columnCounts']['md'],
                'row-cols-lg-'.$elemData['columnCounts']['lg'],
                'row-cols-xl-'.$elemData['columnCounts']['xl']
            ])@if ($elemData['masonry']) data-masonry='{"percentPosition": true }' @endif>

                    @foreach ($elemData['medias'] as $media)
                        @php
                            $src = $media['original_url'];
                            $width = $media['custom_properties']['width'];
                            $height = $media['custom_properties']['height'];

                            if(isset($elemData['conversion']) && !empty($elemData['conversion'])){
                                $src = $media['conversion_urls'][$elemData['conversion']];
                                if ($elemData['conversion'] == 'op_url') {
                                    $width = $media['custom_properties']['op_width'];
                                    $height = $media['custom_properties']['op_height'];
                                } else if ($elemData['conversion'] == 'th_url') {
                                    $width = $media['custom_properties']['th_width'];
                                    $height = $media['custom_properties']['th_height'];
                                } else {
                                    $dimArr = explode('x',str_replace('_url','',$elemData['conversion']));
                                    $width = $dimArr[0];
                                    $height = $dimArr[1];
                                }
                            }

                        @endphp
                        <div @class([
                            'col',
                            'border' => $elemData['slidesBordered'],
                            'overflow-hidden rounded' => $elemData['slidesBordered'],
                            'shadow' => $elemData['slidesShadow']
                        ])>
                            @if ($elemData['lightbox'])
                            <a href="{{ $media['original_url'] }}" data-fancybox="{{$galleryId}}">  
                            @endif
                            <img src="{{ $src }}" loading="lazy" alt="{{ $media['name'] }}" width="{{$width}}" height="{{$height}}" @class([
                                'h-100 w-auto mw-100 object-fit-cover',
                                'rounded' => $elemData['imgRounded'],
                                'shadow' => $elemData['imgShadow'],
                            ])>
                            @if ($elemData['lightbox'])
                            </a>
                            @endif
                        </div>
                    @endforeach

            </div>

        @endswitch

    </div>

@endif