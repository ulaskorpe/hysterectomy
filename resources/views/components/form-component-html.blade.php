@props([
    'formId' => Str::uuid()
])

@php
    $randomFormGuid = Str::uuid();
@endphp

<form class="form needs-validation" action="{{ route('form.submit',$form->id) }}" method="post" novalidate>
    
    @csrf
    @honeypot

    {{-- step count 1 ise normal form acacagiz. bunu kendi icinde kolon sayisina gore yansitiyoruz. step_count 1 den buyuksa swiper basacagiz. --}}

    @switch($form->step_count)
        
        
        @case(1)
        
            @if ($form->column_count === 2)

                @php
                    
                    $firstColumItems = Arr::where($form['json_data'], function ($value,$key) {
                        return $value['itemColumn'] === 1;
                    });

                    $secondColumItems = Arr::where($form['json_data'], function ($value,$key) {
                        return $value['itemColumn'] === 2;
                    });

                @endphp
            
                <div class="row g-3">

                    <div class="col-lg-6">
                        <div class="row g-3">
                            
                            @foreach ($firstColumItems as $item)
                            <x-form-component-field :formId="$formId" :item="$item"/>
                            @endforeach

                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="row g-3">
                            
                            @foreach ($secondColumItems as $item)
                            <x-form-component-field :formId="$formId" :item="$item"/>
                            @endforeach

                            <x-kvkk-check :form="$form"/>
                            
                            <div class="col-12">
                                <button class="btn btn-primary" type="submit">{{$form['button_text']}}</button>
                            </div>

                        </div>
                    </div>

                </div>
            
            @else

                <div class="row g-3">
                    
                    @foreach ($form['json_data'] as $item)
                    <x-form-component-field :formId="$formId" :item="$item"/>
                    @endforeach

                    <x-kvkk-check :form="$form"/>

                    <div class="col-12 text-center pt-2">
                        <button class="btn btn-primary" type="submit">{{$form['button_text']}}</button>
                    </div>

                </div>

            @endif

        @break
        

        @default


        @php

            $swiperId = 'swiper-'.rand(111111,999999);

            $formSwiperOptions = [
                'grabCursor' => false,
                'autoHeight' => true,
                'spaceBetween' => 0,
                'slidesPerView' => 1,
                'touchRatio' => 0,
                'simulateTouch' => false,
                'allowTouchMove' => false,
                'keyboard' => false,
                'mousewheel' => false,
                // 'pagination' => [
                //     'el' => '.swiper-pagination',
                //     'type' => 'progressbar'
                // ],
                'navigation' => false,
                'breakpoints' => [
                    998 => [
                        'autoHeight' => false,
                    ]
                ]
            ];

            $jsonSwiperOptions = json_encode($formSwiperOptions);

        @endphp


        <div id="{{$swiperId}}" @class(['swiper','ea-swiper','position-relative','overlow-hidden']) data-swiper-options="{{$jsonSwiperOptions}}">
            
            {{-- <div class="swiper-pagination"></div> --}}
            
            <div class="swiper-wrapper align-items-lg-stretch">

                @for ($i = 0; $i < $form->step_count; $i++)

                @php
                    $stepItems = Arr::where($form['json_data'], function ($value,$key) use ($i) {
                        return $value['itemStep'] === $i + 1;
                    });
                @endphp

                <div class="swiper-slide h-auto d-flex flex-column">
                    
                    <div class="row g-3">
                        
                        @foreach ($stepItems as $item)
                        <x-form-component-field :formId="$formId" :item="$item"/>
                        @endforeach

                         @if ($i + 1 === $form->step_count)
                        <x-kvkk-check :form="$form"/>
                        @endif

                    </div>

                    <div class="d-flex gap-3 justify-content-center pt-4 mt-auto">

                        @if ($i > 0)
                        <button class="btn btn-sm btn-light rounded-pill px-4" type="button" onclick="stepperFormNextPrev('{{$swiperId}}','prev')">{{ __('Önceki') }}</button>                            
                        @endif

                        @if ($i + 1 === $form->step_count)
                        <button class="btn btn-sm btn-secondary d-flex align-items-center justify-content-center gap-2 flex-grow-1 rounded-pill" type="submit"><i class="bi bi-send"></i>{{$form['button_text']}}</button>
                        @else
                        <button class="btn btn-sm btn-secondary flex-grow-1 rounded-pill" type="button" onclick="stepperFormNextPrev('{{$swiperId}}','next')">{{ __('Devam et') }}</button>  
                        @endif

                    </div>

                    <div class="slide-error fs-sm d-none position-absolute alert alert-danger bottom-0 start-0 zindex-3 p-2 mb-0 w-100 rounded-3">
                        {{ __('Please fill all required fields.') }}
                    </div>

                </div>

                @endfor

            </div>

        </div>
            
            
    @endswitch

</form>