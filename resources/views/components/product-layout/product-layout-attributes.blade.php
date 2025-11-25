@php

    $onlyWithValues = Arr::first($productAttributes, function ($value, $key) {
        return !empty($value['value']);
    });

    $visibleCount = 0;

    //visible konusuna guncel olarak bakmak icin db deki karsiligina bakicaz.
    //bu her biri icin ayrica query calistiyor. urun listelemelerinde kullanildiginda ciddi yuk. kapatiyorum.
    // foreach ($productAttributes as $key => $value) {
    //     $att = \App\Models\ProductAttribute::find($value['id']);
    //     if( $att ){
    //         $productAttributes[$key]['fe_visible'] = $att->fe_visible;

    //         if($productAttributes[$key]['fe_visible'] === true){
    //             $visibleCount++;
    //         }
    //     }
    // }

@endphp

@if ($onlyWithValues)
<div class="fs-sm d-flex flex-column gap-2">
    @foreach ($productAttributes as $option)
    @if (!empty($option['value']) && $option['fe_visible'])
    @if ($withLabel)
    <x-attribute-type :option="$option"/>@if (!$loop->last) @endif
    @else
    <x-attribute-type-value-only :option="$option"/>@if (!$loop->last) @endif
    @endif
    @endif
    @endforeach
</div>
@endif