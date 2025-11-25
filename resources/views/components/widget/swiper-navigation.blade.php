@php
    $swiperId = null;
    if(isset($data['swiperId']) && !empty($data['swiperId'])){
        $swiperId = $data['swiperId'];
    }

    $elemDivClasses = ['d-flex w-auto align-items-center gap-2 h-30px'];

    if($data['baseDesignOptions']['class']) $elemDivClasses[] = $data['baseDesignOptions']['class'];
    if(isset($data['baseDesignOptions']['position'])) $elemDivClasses[] = $data['baseDesignOptions']['position'];

    foreach ($data['baseDesignOptions']['margin'] as $key => $value) {
        if($value) $elemDivClasses[] = $value;
    }
    foreach ($data['baseDesignOptions']['padding'] as $key => $value) {
        if($value) $elemDivClasses[] = $value;
    }

@endphp

@if ($swiperId)
    <div @class($elemDivClasses)>
        <div onclick="document.getElementById('{{$swiperId}}').swiper.slidePrev();" class="box-30 bg-theme-1 cursor-pointer text-white p-0 d-flex align-items-center justify-content-center rounded-circle" tabindex="0" role="button" aria-label="Previous slide">
            <i class="bi bi-arrow-down-left"></i>
        </div>
        <div onclick="document.getElementById('{{$swiperId}}').swiper.slideNext();" class="box-30 bg-theme-1 cursor-pointer text-white p-0 d-flex align-items-center justify-content-center rounded-circle" tabindex="0" role="button" aria-label="Next slide">
            <i class="bi bi-arrow-up-right"></i>
        </div>
    </div>
@endif