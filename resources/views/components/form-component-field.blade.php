@props(['formId' => '','item' => []])

@php
    $name = isset($item['itemInputName']) ? $item['itemInputName'] : Str::slug($item['itemLabel'],'_');
@endphp

<div class="{{ $item['size'] }}">

    @if (in_array($item['type'],['text','number','email','password']))
    <div class="" data-name="{{$name}}">
        @if (!$item['placeholder'])
        <label for="{{$formId.'-'.$name}}" class="form-label fs-sm ps-3">{{ $item['itemLabel'] }}{!! $item['required'] ? ' <span class="text-danger">*</span>' : '' !!}</label>    
        @endif
        <input type="{{$item['type']}}" class="form-control form-control-sm rounded-pill" name="{{$name}}" id="{{$formId.'-'.$name}}" value="{{old($name)}}"  placeholder="{{ $item['placeholderLabel'] ?? $item['itemLabel'] }}" @if($item['required']) required @endif/>
        @if ($item['helper'])
        <span class="fs-xs text-start ps-3">{!! $item['helper'] !!}</span>
        @endif
    </div>
    @endif

    @if ($item['type'] == 'noninput_text')
    <div class="lh-sm" data-name="{{$name}}">
        {!! $item['helper'] !!}
    </div>
    @endif

    @if ($item['type'] == 'range')
    <div class="" data-name="{{$name}}">
        <label for="{{$formId.'-'.$name}}" class="form-label fs-sm ps-3 d-flex align-items-end gap-3">
            <span>{{ $item['itemLabel'] }}{!! $item['required'] ? ' <span class="text-danger">*</span>' : '' !!}</span>
            <span class="range-value bg-primary text-white px-2 py-1 rounded fw-bold">{{$item['rangeOptions']['default']}}</span>
        </label>
        <input oninput="this.closest('div').querySelector('.range-value').innerText = this.value" type="range" name="{{$name}}" class="form-range" min="{{$item['rangeOptions']['min']}}" max="{{$item['rangeOptions']['max']}}" step="{{$item['rangeOptions']['step']}}" id="{{$formId.'-'.$name}}" value="{{$item['rangeOptions']['default']}}">
        @if ($item['helper'])
        <span class="fs-xs text-start ps-3">{!! $item['helper'] !!}</span>
        @endif
    </div>
    @endif

    @if ($item['type'] == 'intTelInput')

        <div class="" data-name="{{$name}}">
            @if (!$item['placeholder'])
            <label for="{{$formId.'-'.$name}}" class="form-label fs-sm ps-3">{{ $item['itemLabel'] }}{!! $item['required'] ? ' <span class="text-danger">*</span>' : '' !!}</label>    
            @endif
            <input type="text" class="form-control form-control-sm rounded-pill int-tel-input" name="{{$name}}" id="{{$formId.'-'.$name}}" value="{{old($name)}}" @if($item['required']) required @endif/>
            @if ($item['helper'])
            <span class="fs-xs text-start ps-3">{!! $item['helper'] !!}</span>
            @endif
        </div>

        @push('footer')
        <x-int-tel-input />
        @endpush

    @endif

    @if ($item['type'] == 'textarea')
    <div class="" data-name="{{$name}}">
        @if (!$item['placeholder'])
        <label for="{{$formId.'-'.$name}}" class="form-label fs-sm ps-3">{{ $item['itemLabel'] }}{!! $item['required'] ? ' <span class="text-danger">*</span>' : '' !!}</label>    
        @endif
        <textarea class="form-control form-control-sm rounded-4" rows="3" name="{{$name}}" id="{{$formId.'-'.$name}}"  placeholder="{{ $item['placeholderLabel'] ?? $item['itemLabel'] }}" @if($item['required']) required @endif>{{old($name)}}</textarea>
        @if ($item['helper'])
        <span class="fs-xs text-start ps-3">{!! $item['helper'] !!}</span>
        @endif
    </div>
    @endif

    @if ($item['type'] == 'checkbox')
    <div class="d-flex flex-column text-start" data-name="{{$name}}">
        <label class="form-label fs-sm">{{ $item['itemLabel'] }}{!! $item['required'] ? ' <span class="text-danger">*</span>' : '' !!}</label>
        <div class="d-flex flex-column checkbox-group bg-gray-50 p-3 border">
            @foreach ($item['options'] as $key => $option)
            <div class="form-check d-flex align-items-center gap-2">
                <input onchange="checkboxRequiredCheck(this)" class="form-check-input" type="checkbox" value="{{ $option }}" name="{{$name}}[]" id="{{$formId.'-'.$name.'-'.$key}}" @if($item['required']) required @endif>
                <label class="form-check-label fs-sm" for="{{$formId.'-'.$name.'-'.$key}}">{{ $option }}</label>
            </div>
            @endforeach
        </div>
        @if ($item['helper'])
        <span class="fs-xs text-start ps-3">{!! $item['helper'] !!}</span>
        @endif
    </div>
    @endif

    @if ($item['type'] == 'radio')
    <div class="d-flex flex-column text-start" data-name="{{$name}}">
        <label class="form-label fs-sm">{{ $item['itemLabel'] }}{!! $item['required'] ? ' <span class="text-danger">*</span>' : '' !!}</label>
        <div class="d-flex flex-column checkbox-group">
            @foreach ($item['options'] as $key => $option)
            <div class="form-check d-flex align-items-center gap-2">
                <input class="form-check-input mt-0" type="radio" value="{{ $option }}" name="{{$name}}" id="{{$formId.'-'.$name.'-'.$key}}" @if($item['required']) required @endif>
                <label class="form-check-label fs-sm" for="{{$formId.'-'.$name.'-'.$key}}">{{ $option }}</label>
            </div>
            @endforeach
        </div>
        @if ($item['helper'])
        <span class="fs-xs text-start ps-3">{!! $item['helper'] !!}</span>
        @endif
    </div>
    @endif

    @if ($item['type'] == 'radioWithIcon')
    <div class="d-flex flex-column align-items-center" data-name="{{$name}}">
        <label class="form-label fs-sm ps-3">{{ $item['itemLabel'] }}{!! $item['required'] ? ' <span class="text-danger">*</span>' : '' !!}</label>
        <div class="d-flex gap-2 justify-content-center checkbox-group flex-wrap">
            @foreach ($item['optionsWithIcon'] as $key => $option)
            <div class="h-100">
                <input class="btn-check icon-check" type="radio" value="{{ $option['value'] }}" name="{{$name}}" id="{{$formId.'-'.$name.'-'.$key}}" @if($item['required']) required @endif>
                <label class="btn btn-outline-light h-100" for="{{$formId.'-'.$name.'-'.$key}}">
                    <div class="d-flex flex-column gap-2 text-primary">
                        <i class="bi bi-circle fs-6"></i>
                        <i class="bi bi-check-circle fs-6"></i>
                        {!! $option['icon'] !!}
                        @if ($option['text'])
                        <span class="fs-xs">{{ $option['value'] }}</span>
                        @endif
                    </div>
                </label>
                @if ($loop->last)
                <span class="invalid-feedback position-absolute start-0 end-0 text-center pt-1">{{ __('Lütfen seçiniz') }}</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if ($item['type'] == 'select')
    <div class="" data-name="{{$name}}">
        @if (!$item['placeholder'])
        <label for="{{$formId.'-'.$name}}" class="form-label fs-sm ps-3">{{ $item['itemLabel'] }}{!! $item['required'] ? ' <span class="text-danger">*</span>' : '' !!}</label>    
        @endif
        <select class="form-select form-select-sm rounded-pill" name="{{$name}}" id="{{$formId.'-'.$name}}"  placeholder="{{ $item['placeholderLabel'] ?? $item['itemLabel'] }}" @if($item['required']) required @endif>
            {{-- <option value="">Lütfen Seçiniz</option> --}}
            @foreach ($item['options'] as $option)
            <option value="{{$option}}">{{$option}}</option>
            @endforeach
        </select>
        @if ($item['helper'])
        <span class="fs-xs text-start ps-3">{!! $item['helper'] !!}</span>
        @endif
    </div>
    @endif

    @if ($item['type'] == 'countrySelect')
        
        @php
            $countries = \App\Models\Country::select('id','name')->get();
        @endphp
        
        <div class="" data-name="{{$name}}">
            @if (!$item['placeholder'])
            <label for="{{$formId.'-'.$name}}" class="form-label fs-sm ps-3">{{ $item['itemLabel'] }}{!! $item['required'] ? ' <span class="text-danger">*</span>' : '' !!}</label>    
            @endif    
            <select onchange="stateSelectedByName('{{$formId}}')" id="states-{{$formId}}" class="form-select form-select-sm rounded-pill" name="{{$name}}" placeholder="{{ $item['placeholderLabel'] ?? $item['itemLabel'] }}" @if($item['required']) required @endif>
                @foreach ($countries as $country)
                <option value="{{$country->name}}" data-country-id="{{$country->id}}" @selected($country->id === 233)>{{$country->name}}</option>
                @endforeach
            </select>
            @if ($item['helper'])
            <span class="fs-xs text-start ps-3">{!! $item['helper'] !!}</span>
            @endif
        </div>

        @push('footer')
        <script>
            const countrySelectSeckin = document.getElementById('states-{{$formId}}');
            const insuranceCompanyInput = document.querySelector('[data-name="insurancecompany"]');
            const insuranceMemberIdInput = document.querySelector('[data-name="insurancememberid"]');
            const insuranceDesc = document.querySelector('[data-name="insurencedesc"]');

            countrySelectSeckin.addEventListener('change',() => {

                if(countrySelectSeckin.value == 'United States'){
                    if(insuranceCompanyInput){
                        insuranceCompanyInput.classList.remove("opacity-0");
                        insuranceCompanyInput.querySelector('input').required = true;
                        insuranceCompanyInput.querySelector('input').value = "";
                        insuranceCompanyInput.querySelector('input').readonly = false;
                    }
                    if(insuranceMemberIdInput){
                        insuranceMemberIdInput.classList.remove("opacity-0");
                        insuranceMemberIdInput.querySelector('input').required = true;
                        insuranceMemberIdInput.querySelector('input').value = "";
                        insuranceMemberIdInput.querySelector('input').readonly = false;
                    }
                    if(insuranceDesc){
                        insuranceDesc.classList.remove("opacity-0");
                    }
                } else {
                    if(insuranceCompanyInput){
                        insuranceCompanyInput.classList.add("opacity-0");
                        insuranceCompanyInput.querySelector('input').required = false;
                        insuranceCompanyInput.querySelector('input').value = "NA";
                        insuranceCompanyInput.querySelector('input').readonly = true;
                    }
                    if(insuranceMemberIdInput){
                        insuranceMemberIdInput.classList.add("opacity-0");
                        insuranceMemberIdInput.querySelector('input').required = false;
                        insuranceMemberIdInput.querySelector('input').value = "NA";
                        insuranceMemberIdInput.querySelector('input').readonly = true;
                    }
                    if(insuranceDesc){
                        insuranceDesc.classList.add("opacity-0");
                    }
                }

            });

        </script>
        @endpush

    @endif

    @if ($item['type'] == 'stateSelect')
    @php
        $states = \App\Models\State::select('id','name')->where('country_id',225)->orderBy('name')->get();
    @endphp
    <div class="" data-name="{{$name}}">
        @if (!$item['placeholder'])
        <label for="{{$formId.'-'.$name}}" class="form-label fs-sm ps-3">{{ $item['itemLabel'] }}{!! $item['required'] ? ' <span class="text-danger">*</span>' : '' !!}</label>    
        @endif
        <select onchange="stateSelectedByName('{{$formId}}')" id="states-{{$formId}}" class="form-select form-select-sm rounded-pill" name="{{$name}}" placeholder="{{ $item['placeholderLabel'] ?? $item['itemLabel'] }}" @if($item['required']) required @endif>
            <option value="">Lütfen Seçiniz</option>
            @foreach ($states as $state)
            <option value="{{Str::replace(' Province','',$state->name)}}" data-state-id="{{$state->id}}">{{Str::replace(' Province','',$state->name)}}</option>
            @endforeach
        </select>
        @if ($item['helper'])
        <span class="fs-xs text-start ps-3">{!! $item['helper'] !!}</span>
        @endif
    </div>
    @endif

    @if ($item['type'] == 'citySelect')
    <div class="" data-name="{{$name}}">
        @if (!$item['placeholder'])
        <label for="{{$formId.'-'.$name}}" class="form-label fs-sm ps-3">{{ $item['itemLabel'] }}{!! $item['required'] ? ' <span class="text-danger">*</span>' : '' !!}</label>    
        @endif
        <select id="cities-{{$formId}}" class="form-select form-select-sm rounded-pill" name="{{$name}}" placeholder="{{ $item['placeholderLabel'] ?? $item['itemLabel'] }}" @if($item['required']) required @endif>
            <option value="">Lütfen öncelikle şehir seçiniz</option>
        </select>
        @if ($item['helper'])
        <span class="fs-xs text-start ps-3">{!! $item['helper'] !!}</span>
        @endif
    </div>
    @endif

    @if ($item['type'] == 'contentList')
    @php
        $selectContents = \App\Models\Content::select('id','name')->whereIn('content_type_id',$item['contentTypes'])->orderBy('content_type_id')->get();
    @endphp
    <div class="" data-name="{{$name}}">
        <select id="{{$formId.'-'.$name}}"  class="form-select form-select-sm rounded-pill" name="{{$name}}" placeholder="{{ $item['placeholderLabel'] ?? $item['itemLabel'] }}" @if($item['required']) required @endif>
            <option value="">{{__('Lütfen Seçiniz')}}</option>
            @foreach ($selectContents as $cont)
            <option value="{{$cont->name}}">{{$cont->name}}</option>
            @endforeach
        </select>
        @if (!$item['placeholder'])
        <label for="{{$formId.'-'.$name}}" class="form-label fs-sm ps-3">{{ $item['itemLabel'] }}{!! $item['required'] ? ' <span class="text-danger">*</span>' : '' !!}</label>    
        @endif
    </div>
    @endif

    @if ($item['type'] == 'contentListCheckBox')
    @php
        $selectContents = \App\Models\Content::select('id','name')->whereIn('content_type_id',$item['contentTypes'])->orderBy('content_type_id')->get();
    @endphp
    <div class="" data-name="{{$name}}">
        @if (!$item['placeholder'])
        <label for="{{$formId.'-'.$name}}" class="form-label fs-sm ps-3">{{ $item['itemLabel'] }}{!! $item['required'] ? ' <span class="text-danger">*</span>' : '' !!}</label>    
        @endif
        <div class="d-block column-count-lg-3 bg-gray-50 p-3 border">
            @foreach ($selectContents as $key => $cont)
            <div class="form-check">
                <input onchange="checkboxRequiredCheck(this)" class="form-check-input" type="checkbox" value="{{ $cont->name }}" name="{{$name}}[]" id="{{$formId.'-'.$name.'-'.$key}}" @if($item['required']) required @endif>
                <label class="form-check-label" for="{{$formId.'-'.$name.'-'.$key}}">{{ $cont->name }}</label>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>