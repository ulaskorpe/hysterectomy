@php
    
    $display = true;

    if( $isMobile && !$column['settings']['display']['mobile'] ){
        $display = false;
    }

    if( !$isMobile && !$column['settings']['display']['desktop'] ){
        $display = false;
    }

    $classes = [];
    $elemDivClasses = ['d-flex'];

    if( ! $tabOrSlideDiv ) {

        foreach ($column['settings']['size'] as $key => $value) {
            if($value) $classes[] = $value;
        }

        if( isset($column['settings']['sticky']) && $column['settings']['sticky'] ){

            $elemDivClasses[] = 'sticky-top sticky-column';

        } else {

            $elemDivClasses[] = 'position-relative';
            $elemDivClasses[] = 'h-100';

        }
    } else {

        $classes[] = 'position-relative';
        $classes[] = 'h-100';
        $elemDivClasses[] = 'h-100';

    }

    if($column['settings']['class']) $elemDivClasses[] = $column['settings']['class'];

    foreach ($column['settings']['margin'] as $key => $value) {
        if($value) $elemDivClasses[] = $value;
    }
    foreach ($column['settings']['padding'] as $key => $value) {
        if($value) $elemDivClasses[] = $value;
    }
    foreach ($column['settings']['flexDirection'] as $key => $value) {
        if($value) $elemDivClasses[] = $value;
    }

    if(isset($column['settings']['rounded'])) $elemDivClasses[] = $column['settings']['rounded'];


@endphp

@if ( $display )

<div @class($classes) id="{{$column['settings']['id']}}">

    <div @class($elemDivClasses)>

        @if ($column['settings']['background']['color'])
        <div @class(['overlay',$column['settings']['background']['color']])></div>
        @endif

        @if ( isset($column['settings']['background']['dynamic']) && $column['settings']['background']['dynamic'] == true && $column['settings']['background']['dynamic_image'])
            
            @if ($column['settings']['background']['dynamic_image'] == 'header_video' && isset($content->header_video[0]))
            <video class="bg-video" autoplay muted loop playsinline poster="{{ isset($content->media_objects['headerImage']) ? $content->media_objects['headerImage']->original_url : '' }}">
                <source src="{{$content->header_video[0]->original_url}}" type="video/mp4">
            </video>
            @else
            @php
                $bgDynamicImage = null;
                if( $column['settings']['background']['dynamic_image'] == 'header_image' && isset($content->media_objects['headerImage']) ){
                    $bgDynamicImage = $content->media_objects['headerImage'];
                }
                if( $column['settings']['background']['dynamic_image'] == 'main_image' && isset($content->media_objects['mainImage']) ){
                    $bgDynamicImage = $content->media_objects['mainImage'];
                }
            @endphp
            @if ($bgDynamicImage)
            <div @class(['overlay',$column['settings']['background']['size'],$column['settings']['background']['position']]) data-bg-image="{{$bgDynamicImage->original_url}}"></div>
            @endif
            @endif
       
        @elseif ($column['settings']['background']['image'])
            <div @class(['overlay',$column['settings']['background']['size'],$column['settings']['background']['position']]) data-bg-image="{{$column['settings']['background']['image']}}"></div>
        @endif

        @if ($column['settings']['background']['layer']['color'])
        <div @class(['overlay',$column['settings']['background']['layer']['color'],$column['settings']['background']['layer']['opacity']])></div>
        @endif
        
        @if (count($column['elements']) > 0)
        
            @foreach ($column['elements'] as $element)
        
            <x-column-element :content="$content" :element="$element"/>
    
            @endforeach
        
        @endif

    </div>

</div>
    
@endif