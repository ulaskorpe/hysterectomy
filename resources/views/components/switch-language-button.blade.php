@props([
    'href' => "",
    'language' => config('languages.default'),
    'active' => false
])

<li>
    <a href="{{$href}}" @class(['dropdown-item text-center fs-sm']) title="{{ Str::upper($language)}}">
        {{$language == 'ar' ? 'العربية' : Str::upper($language)}}
    </a>
</li>