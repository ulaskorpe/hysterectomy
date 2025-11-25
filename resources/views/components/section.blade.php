@php
    
    $display = true;

    if( $isMobile && !$section['settings']['display']['mobile'] ){
        $display = false;
    }

    if( !$isMobile && !$section['settings']['display']['desktop'] ){
        $display = false;
    }

    $classes = ['d-flex','flex-column','position-relative','content-section'];

    if(isset($section['settings']['fadeInUp']) && $section['settings']['fadeInUp']) {
        $classes[] = 'animate__animated';
        $classes[] = 'anim-opacity-0';
    }

    if($section['settings']['class']) $classes[] = $section['settings']['class'];
    foreach ($section['settings']['margin'] as $key => $value) {
        if($value) $classes[] = $value;
    }
    foreach ($section['settings']['padding'] as $key => $value) {
        if($value) $classes[] = $value;
    }

    $bgExtraClasses = ['overlay'];
    $bgAnimation = false;
    $bgAnimationClass = "";

    if(isset($section['settings']['background']['relativeSize'])) {
        $bgExtraClasses[] = $section['settings']['background']['relativeSize'];
    }

    if(isset($section['settings']['background']['relativePosition'])) {
        $bgExtraClasses[] = $section['settings']['background']['relativePosition'];
    }

    if(isset($section['settings']['background']['animated']) && isset($section['settings']['background']['animation']) && $section['settings']['background']['animated'] === true && !empty($section['settings']['background']['animation'])) {
        $bgExtraClasses[] = 'animate__animated anim-opacity-0';
        $bgAnimation = true;
        $bgAnimationClass = $section['settings']['background']['animation'];
    }
    

@endphp

@if ( $display )

    <section @class($classes) id="{{$section['settings']['id']}}"@if (isset($section['settings']['fadeInUp']) && $section['settings']['fadeInUp']) data-animation="{{$bgAnimationClass}}"@endif>

        @if (isset($section['settings']['shapeTop']) && $section['settings']['shapeTop']['active'])
    
            @switch($section['settings']['shapeTop']['type'])

                @case("waves-opacity")
                
                <div @class(['shape-waves-opacity','top',$section['settings']['shapeTop']['color'] => !empty($section['settings']['shapeTop']['color'])])>
                    <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none"> <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" opacity=".25" class="shape-fill"></path> <path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" opacity=".5" class="shape-fill"></path> <path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z" class="shape-fill"></path> </svg>
                </div>
                
                @break
                
                @case("wave")
                
                <div @class(['shape-wave','top',$section['settings']['shapeTop']['color'] => !empty($section['settings']['shapeTop']['color'])])>
                    <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                        <path d="M985.66,92.83C906.67,72,823.78,31,743.84,14.19c-82.26-17.34-168.06-16.33-250.45.39-57.84,11.73-114,31.07-172,41.86A600.21,600.21,0,0,1,0,27.35V120H1200V95.8C1132.19,118.92,1055.71,111.31,985.66,92.83Z" class="shape-fill"></path>
                    </svg>
                </div>
                
                @break
                
                @default
                    
            @endswitch


        @endif

        @if ($section['settings']['background']['color'])
        <div @class([implode(' ',$bgExtraClasses),$section['settings']['background']['color']])@if ($bgAnimation) data-animation="{{$bgAnimationClass}}" @endif></div>
        @endif

        @if ($section['settings']['background']['image'] && !$section['settings']['background']['parallax'])
        <div @class([implode(' ',$bgExtraClasses),$section['settings']['background']['size'],$section['settings']['background']['position']]) data-bg-image="{{$section['settings']['background']['image']}}"@if ($bgAnimation) data-animation="{{$bgAnimationClass}}" @endif></div>
        @endif

        @if ($section['settings']['background']['image'] && $section['settings']['background']['parallax'])
        <div class="parallax" data-parallax-image="{{$section['settings']['background']['image']}}"></div>
        @endif

        @if ($section['settings']['background']['layer']['color'])
        <div @class([implode(' ',$bgExtraClasses),'delay-100ms',$section['settings']['background']['layer']['color'],$section['settings']['background']['layer']['opacity']])@if ($bgAnimation) data-animation="{{$bgAnimationClass}}" @endif></div>
        @endif

        @if (count($section['containers']) > 0)

            @if (isset($section['settings']['containerAsTabs']) && $section['settings']['containerAsTabs'] === true)

            <div class="container position-relative">
                
                @if (!empty($section['settings']['name']) && $section['settings']['name'] != 'Section')
                    <h2 class="text-center mb-4">{{$section['settings']['name']}}</h2>
                @endif

                <div class="d-flex flex-column align-items-center">

                    @if ($isMobile)
                    <ul class="nav nav-tabs pb-1 d-flex flex-row flex-nowrap gap-1 mb-4 position-static overflow-auto w-100" role="tablist">
                        @foreach ($section['containers'] as $container)
                        <li class="nav-item" role="presentation">
                            <button @class(['btn btn-light btn-sm px-2 py-1 text-nowrap fw-semibold', 'active' => $loop->first]) data-bs-toggle="tab" data-bs-target="#{{$container['settings']['id']}}" type="button" role="tab" aria-controls="{{$container['settings']['id']}}" aria-selected="true">{!! $container['settings']['name'] !!}</button>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <ul class="nav nav-tabs pb-1 d-inline-flex gap-1 mb-4 justify-content-center border-0" role="tablist">
                        @foreach ($section['containers'] as $container)
                        <li class="nav-item" role="presentation">
                            <button @class(['btn font-title px-4 lead-responsive border-0 fw-semibold', 'active' => $loop->first]) data-bs-toggle="tab" data-bs-target="#{{$container['settings']['id']}}" type="button" role="tab" aria-controls="{{$container['settings']['id']}}" aria-selected="true">{!! $container['settings']['name'] !!}</button>
                        </li>
                        @endforeach
                    </ul>
                    @endif

                    <div class="tab-content w-100">
                        @foreach ($section['containers'] as $container)
                        <div @class(['tab-pane','show active' => $loop->first]) id="{{$container['settings']['id']}}" role="tabpanel" tabindex="0">
                            <x-container :container="$container" :content="$content"/>
                        </div>
                        @endforeach
                    </div>

                </div>

            </div>

            @elseif (isset($section['settings']['containerAsSlides']) && $section['settings']['containerAsSlides'] === true)

                @php

                    $sectionSwiperOptions = [
                        'grabCursor' => true,
                        'slidesPerView' => 1,
                        'spaceBetween' => 0,
                        'navigation' => [
                            'nextEl' => ".swiper-button-next",
                            'prevEl' => ".swiper-button-prev",
                        ]
                    ];

                    $jsonSwiperOptions = json_encode($sectionSwiperOptions);

                @endphp

                <div @class(['position-relative','zindex-2'])>
                    <div id="swiper-{{$section['settings']['id']}}" @class(['swiper','ea-swiper','position-relative','overlow-hidden','pb-0']) data-swiper-options="{{$jsonSwiperOptions}}">
                        <div class="swiper-wrapper h-100">
                            @foreach ($section['containers'] as $container)
                            <div class="swiper-slide my-0 overlow-hidden h-100 position-relative">
                                <x-container :container="$container" :content="$content"/>
                            </div>
                            @endforeach
                        </div>
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-button-next"></div>
                    </div>
                </div>

            @else

            @foreach ($section['containers'] as $container)
            <x-container :container="$container" :content="$content" :is-layout="$isLayout"/>
            @endforeach
            
            @endif

        @endif

        @if (isset($section['settings']['shapeBottom']) && $section['settings']['shapeBottom']['active'])
    
            @switch($section['settings']['shapeBottom']['type'])

                @case("waves-opacity")
                
                <div @class(['shape-waves-opacity',$section['settings']['shapeBottom']['color'] => !empty($section['settings']['shapeBottom']['color'])])>
                    <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none"> <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" opacity=".25" class="shape-fill"></path> <path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" opacity=".5" class="shape-fill"></path> <path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z" class="shape-fill"></path> </svg>
                </div>
                
                @break
                
                @case("wave")
                
                <div @class(['shape-wave',$section['settings']['shapeBottom']['color'] => !empty($section['settings']['shapeBottom']['color'])])>
                    <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                        <path d="M985.66,92.83C906.67,72,823.78,31,743.84,14.19c-82.26-17.34-168.06-16.33-250.45.39-57.84,11.73-114,31.07-172,41.86A600.21,600.21,0,0,1,0,27.35V120H1200V95.8C1132.19,118.92,1055.71,111.31,985.66,92.83Z" class="shape-fill"></path>
                    </svg>
                </div>
                
                @break
                
                @default
                    
            @endswitch


        @endif

    </section>
    
@endif