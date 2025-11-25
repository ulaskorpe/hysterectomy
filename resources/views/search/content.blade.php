<x-fe-layout :bread-crumb="['data' => [],'title' => $queryParam ?? $settings->site_name]">

    <x-page-header-static :title="__('Arama sonuçları:').' '.$queryParam" />

    <section class="content-section pt-5">
        <div class="container">

            <div class="mb-5">
                <form class="d-flex flex-grow-1 mx-auto needs-validation" novalidate method="GET" action="{{route('search.content')}}">
                    <div class="input-group overflow-hidden border bg-light">
                        <span class="input-group-text border-0 outline-0 bg-opacity-10 bg-light"><i class="bi bi-search"></i></span>
                        <input type="hidden" name="language" value="{{app()->getLocale()}}">
                        <input name="search" value="{{$queryParam}}" type="text" class="form-control bg-opacity-10 bg-light border-0 outline-0 ps-0 shadow-none ms-0" placeholder="{{__('Arama...')}}" aria-label="Ara..." required>
                    </div>
                </form>
            </div>

            @if (Str::length($queryParam) < 3)
            <div class="alert alert-danger">
                {{ __('Lütfen en az 3 harf giriniz.') }}
            </div>
            @endif

            @if ($searchResults && $searchResults->count() === 0)
            <div class="alert alert-danger">
                {{ __('Aramanıza uygun kayıt bulunamadı.') }}
            </div>
            @endif

            @if ($searchResults)
            <div class="row g-4 g-xl-5 row-cols-1 row-cols-md-2 row-cols-lg-3">
                @foreach($searchResults->groupByType() as $type => $modelSearchResults)

                   @foreach ($modelSearchResults as $item)
                    <div class="col">
                        <x-cards.content-card :content="$item->searchable"/>
                    </div>
                    @endforeach

                @endforeach
            </div>
            @endif
            
        </div>
    </section>

    <x-slot name="headerScripts">
        
    </x-slot>

    <x-slot name="footerScripts">
        
    </x-slot>

</x-fe-layout>