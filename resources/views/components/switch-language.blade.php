@props([
    'connecteds' => []
])

<ul class="dropdown-menu dropdown-language">
    
    @foreach (config('languages.active') as $lang)

    @php
            
        //icerigin dilde karsiligi var mi?
        $lang_exists = Arr::first($connecteds, function ($value, $key) use ($lang) {
            return $value['language'] === $lang;
        });

    @endphp

    {{--
    @if (!$loop->first)
    <div class="vr bg-gray-500"></div>
    @endif
    --}}

    @if ($lang_exists )


        @if ( $lang_exists->uuid == $settings->home_page )
            @if ($lang_exists['language'] == config('languages.default'))
            <x-switch-language-button :href="config('app.url')" :language="$lang" :active="$lang == app()->getLocale()"/>
            @else
            <x-switch-language-button :href="config('app.url') .'/'. $lang" :language="$lang" :active="$lang == app()->getLocale()"/>
            @endif
        @else
            <x-switch-language-button :href="$lang_exists['uri']['final_uri']" :language="$lang" :active="$lang == app()->getLocale()"/>
        @endif


    @else
    
        @if ($lang == config('languages.default'))
        <x-switch-language-button :href="config('app.url')" :language="$lang" :active="$lang == app()->getLocale()"/>
        @else
        <x-switch-language-button :href="config('app.url') .'/'. $lang" :language="$lang" :active="$lang == app()->getLocale()"/>
        @endif

    @endif

    @endforeach

</ul>