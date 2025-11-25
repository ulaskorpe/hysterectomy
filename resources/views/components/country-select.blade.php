@props([
    'countryId' => null
])

@php
    //$countries = \App\Models\Country::orderBy('native','ASC')->get();
    $turkiye = \App\Models\Country::where('phonecode',90)->first();
@endphp

{{--
<select name="country_id" id="countries-{{$guid}}" class="form-select" required onchange="countrySelected('{{$guid}}')">
    <option value="">{{__('Lütfen Seçiniz')}}</option>
    @foreach ($countries as $country)
    <option value="{{$country->id}}" @selected($countryId ? $country->id == $countryId : $country->phonecode == 90)>{{$country->native}}</option>
    @endforeach
</select>
--}}

<select name="country_id" id="countries-{{$guid}}" class="form-select" required>
    <option value="{{$turkiye->id}}" selected>Türkiye</option>
</select>