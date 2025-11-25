<x-fe-layout :bread-crumb="['data' => $content->uri->breadcrumb ?? [],'title' => $content->name]">

    @if (data_get($content->additional, 'hideHeader') === false)
    <x-page-header :content="$content"/>
    @endif

    @if ($content->content && count($content->content) > 0)
        @foreach ($content->content as $section)
        <x-section :section="$section" :content="$content"/>
        @endforeach
    @endif

    @php
        $customCardLayout = null;
        if(isset($content->additional['cardLayout']) && !empty($content->additional['cardLayout'])){
            $cardLayout = \App\Models\CardLayout::find($content->additional['cardLayout']);
            if( $cardLayout ){
                $customCardLayout = $cardLayout;
            }
        }
    @endphp

    <x-content-list :category="$content" :use-date="true" :card-layout="$customCardLayout"/>

    <x-slot name="headerScripts">
        @if (isset($content->additional['headerScripts']))
        {!! $content->additional['headerScripts'] !!}
        @endif
    </x-slot>

    <x-slot name="footerScripts">
        @if (isset($content->additional['footerScripts']))
        {!! $content->additional['footerScripts'] !!}
        @endif
    </x-slot>

</x-fe-layout>