@php

    $visibleCount = 0;

    //visible konusuna guncel olarak bakmak icin db deki karsiligina bakicaz.
    foreach ($productCustomerAttributes as $key => $value) {
        $att = \App\Models\ProductCustomerAttribute::find($value['id']);
        if( $att ){
            $productCustomerAttributes[$key]['fe_visible'] = $att->fe_visible;

            if($productCustomerAttributes[$key]['fe_visible'] === true){
                $visibleCount++;
            }
        }
    }

@endphp

@if ($visibleCount > 0)
<div class="row g-3">
    @foreach ($productCustomerAttributes as $option)
    @if ($option['fe_visible'])
    
    <div @class(['col-12' => !$option['option_type'] != 'date','col-md-6' => $option['option_type'] == 'date'])>
        <div class="d-flex flex-column mb-2">
            <label for="customer_attribute_{{ $option['id'] }}" class="fs-sm fw-semibold mb-0">{{ $option['name'] }}</label>
            @if ($option->desc)
                <span class="fs-xs">{{ $option->desc }}</span>
            @endif
        </div>
        @switch($option['option_type'])
            
            @case('input')
            <input type="text" class="form-control bg-white" name="customer_attributes[{{ $option['id'] }}]" id="customer_attribute_{{ $option['id'] }}" @required($option['is_required']) autocomplete="off"/>
            @break
            
            @case('textarea')
            <textarea class="form-control bg-white" name="customer_attributes[{{ $option['id'] }}]" id="customer_attribute_{{ $option['id'] }}" @required($option['is_required']) autocomplete="off"></textarea>
            @break

            @case('select')
            <select class="form-select form-select-sm" name="customer_attributes[{{ $option['id'] }}]" id="customer_attribute_{{ $option['id'] }}" @required($option['is_required'])>
                <option value="">{{ __('Lütfen Seçiniz') }}</option>
                @foreach ($option['values'] as $value)
                <option value="{{ $value['name'] }}">{{ $value['name'] }}</option>
                @endforeach
            </select>
            @break

            @case('date')
            <input type="text" class="form-control bg-white pickaday" name="customer_attributes[{{ $option['id'] }}]" id="customer_attribute_{{ $option['id'] }}" @required($option['is_required']) autocomplete="off"/>
            @break

            @default
                
        @endswitch

    </div>
    
    @endif
    @endforeach
</div>
@endif