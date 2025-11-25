<x-fe-layout>

    <x-page-header-static :title="$content->name" :summary="$content->summary"/>

    <x-slot name="headerScripts">
        @if (isset($content->additional['headerScripts']))
        {!! $content->additional['headerScripts'] !!}
        @endif
    </x-slot>

    <section class="content-section">
        <div class="container">
            <x-product-list :category="$content"/>
        </div>
    </section> 

    <x-slot name="footerScripts">
        @if (isset($content->additional['footerScripts']))
        {!! $content->additional['footerScripts'] !!}
        @endif
    </x-slot>

</x-fe-layout>