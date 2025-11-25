@php
    
    $elemData = $element['data'];
    $selectedAttribute = $elemData['attribute'];
    $contentAttribute = null;

    if($content->attributes && !empty($content->attributes)){
        
        $contentAttribute = Arr::first($content->attributes, function ($value, $key) use($selectedAttribute) {
            return $value['id'] === $selectedAttribute['id'];
        });

    }

@endphp


@if ($contentAttribute && !empty($contentAttribute['value']))
<div @class(getBaseAndAnimClasses($elemData))>
    @switch($contentAttribute['option_type'])

        @case('youtube')
        <div class="position-relative">
            @if ($content->media_objects['mainImage'])
            <img src="{{$content->media_objects['mainImage']['original_url']}}" class="img-fluid rounded" width="{{$content->media_objects['mainImage']['custom_properties']['width']}}" height="{{$content->media_objects['mainImage']['custom_properties']['height']}}" alt="{{$content->name}}">
            @endif
            <div class="overlay bg-dark opacity-25 rounded"></div>
            <a href="{{$contentAttribute['value']}}" class="position-absolute top-0 start-0 d-flex w-100 h-100 rounded align-items-center justify-content-center" data-fancybox="{{$content->id}}">
                <div class="d-flex box-60 bg-primary text-white align-items-center justify-content-center rounded-circle border border-2 border-white">
                    <i class="bi bi-play-fill fs-2"></i>
                </div>
            </a>
        </div>
        @break

        @case('multiselect')
        @foreach ($contentAttribute['value'] as $val)
        {{$val}}{{ !$loop->last ? ', ' : '' }}
        @endforeach
        @break

        @case('image')
        @php
            $imageWidthHeight = getImageWithHeight($contentAttribute['value']);
        @endphp
        <img loading="lazy" src="{{$contentAttribute['value']}}" class="img-fluid" @if (!empty($imageWidthHeight)) width="{{$imageWidthHeight[0]}}" height="{{$imageWidthHeight[0]}}" @endif alt="{{$contentName}}" />
        @break

        @case('reviewSites')
        @php
            $svgId = rand(111111,999999);
        @endphp
        @if ($contentAttribute['value'] == 'googleReviews')
            <div class="d-flex align-items-center gap-2">
                <span>
                    <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <mask id="mask0_{{$svgId}}" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="0" y="0" width="23" height="23">
                            <path d="M0.457077 11.463C0.452853 12.933 0.739279 14.3894 1.29988 15.7483C1.86048 17.1072 2.6842 18.3419 3.72367 19.3814C4.76313 20.4209 5.99784 21.2446 7.35677 21.8052C8.7157 22.3658 10.1721 22.6522 11.6421 22.648C17.2351 22.648 22.3191 18.58 22.3191 11.463C22.3099 10.7777 22.2247 10.0955 22.0651 9.42899H11.6421V13.751H17.6421C17.0831 16.496 14.7421 18.072 11.6421 18.072C9.88913 18.072 8.20798 17.3756 6.96846 16.1361C5.72893 14.8966 5.03258 13.2154 5.03258 11.4625C5.03258 9.70954 5.72893 8.02839 6.96846 6.78887C8.20798 5.54934 9.88913 4.85299 11.6421 4.85299C13.1443 4.85113 14.6003 5.37229 15.7601 6.32699L19.0141 3.06899C17.4013 1.64483 15.4115 0.716936 13.2839 0.396917C11.1563 0.0768978 8.98149 0.378383 7.02116 1.26511C5.06084 2.15183 3.39844 3.58604 2.23393 5.3952C1.06942 7.20437 0.4524 9.31144 0.457077 11.463Z" fill="white"/>
                        </mask>
                    <g mask="url(#mask0_{{$svgId}})">
                        <path d="M-0.55957 18.073V4.854L8.08343 11.464L-0.55957 18.073Z" fill="#FBBC05"/>
                        <path d="M-0.55957 4.85399L8.08343 11.461L11.6404 8.36099L23.8404 6.37899V-0.739014H-0.55957V4.85399Z" fill="#EA4335"/>
                        <path d="M-0.55957 18.073L14.6924 6.37899L18.7094 6.88799L23.8404 -0.739014V23.661H-0.55957V18.073Z" fill="#34A853"/>
                        <path d="M23.8408 23.666L8.08381 11.464L6.0498 9.939L23.8408 4.854V23.666Z" fill="#4285F4"/>
                    </g>
                    </svg>
                </span>
                <span class="fw-bold lh-1">Google</span>
            </div>
        @endif
        @if ($contentAttribute['value'] == 'trustPilot')
            <div class="d-flex align-items-center gap-2">
                <span>
                    <svg width="26" height="24" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M25.6199 9.49505H16.1889L13.2759 0.675049L10.3519 9.49505L0.919922 9.48605L8.55892 14.942L5.63692 23.752L13.2759 18.305L20.9059 23.752L17.9919 14.942L25.6199 9.49505Z" fill="#04DA8D"/>
                    </svg>
                </span>
                <span class="fw-bold lh-1">Trustpilot</span>
            </div>
        @endif
        @break

        @case('starCount')
        @php
            $starHtml = generateStar($contentAttribute['value']);
        @endphp
        {!! $starHtml !!}
        @break

        @default

        <div class="d-flex flex-column align-items-center justify-content-end">
            @if ($contentAttribute['icon_uri'])
                <img loading="lazy" class="mb-2" src="{{ $contentAttribute['icon_uri'] }}" alt="{{ $contentAttribute['name'] }}">
            @endif
            @if ($elemData['withLabel'])
                <span class="fw-bolder">{{ $contentAttribute['name'] }}</span>
            @endif
            <span>{{ $contentAttribute['value'] }}</span>
        </div>
            
    @endswitch
</div>
@endif