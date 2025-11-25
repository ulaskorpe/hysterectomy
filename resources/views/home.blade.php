<x-fe-layout :content="$content" :is-home="true">

    @if ($content->slider && $content->slider->slides)

        @php

            $swiperOptions = [
                'loop' => false,
                //'centeredSlides' => true,
                'lazyPreloadPrevNext' => 0,
                // 'pagination' => [
                //     'el' => '.swiper-pagination',
                //     'clickable' => true,
                //     'dynamicBullets' => true
                // ],
                'autoplay' => [
                    'delay' => 7000,
                    'disableOnInteraction' => true
                ],
                'grabCursor' => true,
                'slidesPerView' => 1,
                'spaceBetween' => 0,
                'navigation' => [
                    'nextEl' => '.swiper-button-next',
                    'prevEl' => '.swiper-button-prev'
                ]
            ];

            $jsonOptions = json_encode($swiperOptions);

        @endphp

        <div class="position-relative dvh-100 text-center">
            <div class="swiper ea-swiper h-100 opacity-100 zindex-1" data-swiper-options="{{$jsonOptions}}">
                <div class="swiper-wrapper h-100">
                    @foreach ($content->slider->slides as $slide)
                    <div class="swiper-slide h-100 d-flex align-items-center">
                        
                        @if ($isMobile && $slide->mobile_image)
                        <img @if(!$loop->first)loading="lazy"@endif class="w-100 h-100 object-fit-cover position-absolute start-0" src="{{ $slide->mobile_image['original_url'] }}" width="{{ $slide->mobile_image['custom_properties']['width'] }}" height="{{ $slide->mobile_image['custom_properties']['height'] }}">
                        @elseif ($slide->image)
                        <img @if(!$loop->first)loading="lazy"@endif class="w-100 h-100 object-fit-cover position-absolute start-0" src="{{ $slide->image['original_url'] }}" width="{{ $slide->image['custom_properties']['width'] }}" height="{{ $slide->image['custom_properties']['height'] }}">
                        @endif

                        <div class="container position-relative zindex-2">
                            @if ($slide->title)
                            <div class="bigtitle-responsive fw-light font-title">{!! $slide->title !!}</div>
                            @endif
                            @if ($slide->description)
                            <p class="subtitle-responsive mb-0">{!! $slide->description !!}</p>
                            @endif
                            @if ($slide->json_data['buttons']['button_1']['active'] || $slide->json_data['buttons']['button_2']['active'])
                            <div class="d-flex justify-content-center align-items-center gap-2 mt-4">
                                @if ($slide->json_data['buttons']['button_1']['active'])
                                <a href="{{$slide->json_data['buttons']['button_1']['link']}}" class="btn btn-primary px-lg-5">
                                    {{$slide->json_data['buttons']['button_1']['text']}}
                                </a>
                                @endif
                                @if ($slide->json_data['buttons']['button_2']['active'])
                                <a href="{{$slide->json_data['buttons']['button_2']['link']}}" class="btn btn-secondary">
                                    {{$slide->json_data['buttons']['button_2']['text']}}
                                </a>
                                @endif
                            </div>
                            @endif
                        </div>

                    </div>
                    @endforeach
                </div>
            </div>
            <div class="position-absolute zindex-2 text-center bottom-0 pb-4 pb-lg-6 w-100 start-0">
                <svg width="65" height="56" viewBox="0 0 65 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <mask id="mask0_1_2580" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="0" y="0" width="65" height="56">
                        <path d="M64.524 0.155029H0V55.999H64.524V0.155029Z" fill="white"/>
                    </mask>
                    <g mask="url(#mask0_1_2580)">
                        <path d="M64.524 53.563L37.677 38.063C36.4586 37.3596 35.4467 36.3478 34.7432 35.1294C34.0397 33.911 33.6692 32.5289 33.669 31.122V0.155029H30.855V31.121C30.8548 32.528 30.4843 33.91 29.7808 35.1284C29.0773 36.3468 28.0654 37.3586 26.847 38.062L0 53.563L1.407 56L28.254 40.5C29.4724 39.7966 30.8546 39.4262 32.2615 39.4262C33.6684 39.4262 35.0506 39.7966 36.269 40.5L63.116 56L64.524 53.563Z" fill="black"/>
                    </g>
                </svg>
            </div>
        </div>

    @endif

    @if (count($content->content) > 0)
        @foreach ($content->content as $section)
        <x-section :section="$section" :content="$content"/>
        @endforeach
    @endif

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