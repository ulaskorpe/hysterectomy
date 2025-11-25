@php
    
    $subCategories = \App\Models\ContentCategory::select('id','name','parent_id','slug')->with('contents:id,name,summary')->where('parent_id',$content->id)->get();

@endphp

<x-fe-layout :bread-crumb="['data' => $content->uri->breadcrumb ?? [],'title' => $content->name]">

    @if (data_get($content->additional, 'hideHeader') === false)
    <x-page-header :content="$content"/>
    @endif

    @if ($content->content && count($content->content) > 0)
        @foreach ($content->content as $section)
        <x-section :section="$section"/>
        @endforeach
    @endif

    @if ($subCategories->count() > 0)
    
        <div class="content-section bg-dark" id="sss-section">
            <div class="container">
                <div class="d-flex flex-column gap-5 mx-auto mw-xl-75 px-xxl-5">

                    <div class="form-floating position-relative">
                        <input onkeyup="faqSearch()" type="text" class="form-control rounded-pill ps-4 pe-10" id="sss-search" placeholder="{{__(' Sorular İçinde Ara')}}"/>
                        <label for="sss-search" class="px-4 fw-semibold">{{__(' Sorular İçinde Ara')}}</label>
                        <i class="bi bi-search fs-4 pe-6 position-absolute top-50 start-100 translate-middle"></i>
                    </div>

                    <ul class="nav d-flex justify-content-center">
                        @foreach ($subCategories as $sub)
                        <li @class(['nav-item border-start border-white px-2','border-end' => $loop->last])>
                            <a href="#{{$sub->slug}}" class="text-white fw-semibold text-decoration-none">{{ $sub->name }}</a>
                        </li>
                        @endforeach
                    </ul>

                    @foreach ($subCategories as $sub)
                    @php
                    $accId = 'acc-'.Str::uuid();
                    @endphp
                    <div class="d-flex flex-column gap-2" id="{{$sub->slug}}">
                        <h3 class="h6 text-white mb-0 font-body fw-bold ps-4">{{ $sub->name }}</h3>
                        <div class="accordion vstack gap-1" id="{{$accId }}">
                            @foreach ($sub->contents as $key => $item)
                            <div class="accordion-item bg-gray-500 border-0 rounded-5 overflow-hidden">
                                <h4 class="accordion-header font-body">
                                    <button class="accordion-button bg-gray-500 px-4 py-3 fw-normal collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#acc-collapse-{{$sub->id}}-{{$item->id}}" aria-expanded="true" aria-controls="acc-collapse-{{$item->id}}">
                                        {!! $item->name !!}
                                    </button>
                                </h4>
                                <div id="acc-collapse-{{$sub->id}}-{{$item->id}}" class="accordion-collapse collapse" data-bs-parent="#{{$accId }}">
                                <div class="accordion-body pt-0 px-4 pb-3">
                                    {!! $item->summary !!}
                                </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                </div>
            </div>
        </div>

    @else
    
        <div class="content-section bg-dark" id="sss-section">
            <div class="container">
                <div class="d-flex flex-column gap-5 mx-auto mw-xl-75 px-xxl-5">

                    <div class="form-floating position-relative">
                        <input onkeyup="faqSearch()" type="text" class="form-control rounded-pill ps-4 pe-10" id="sss-search" placeholder="{{__(' Sorular İçinde Ara')}}"/>
                        <label for="sss-search" class="px-4 fw-semibold">{{__(' Sorular İçinde Ara')}}</label>
                        <i class="bi bi-search fs-4 pe-6 position-absolute top-50 start-100 translate-middle"></i>
                    </div>

                    @php
                    $accId = 'acc-'.Str::uuid();
                    @endphp
                    <div class="accordion vstack gap-1" id="{{$accId }}">
                        @foreach ($content->contents as $key => $item)
                        <div class="accordion-item bg-gray-500 border-0 rounded-5 overflow-hidden">
                            <h4 class="accordion-header font-body">
                                <button class="accordion-button bg-gray-500 px-4 py-3 fw-normal collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#acc-collapse-{{$item->id}}" aria-expanded="true" aria-controls="acc-collapse-{{$item->id}}">
                                    {!! $item->name !!}
                                </button>
                            </h4>
                            <div id="acc-collapse-{{$item->id}}" class="accordion-collapse collapse" data-bs-parent="#{{$accId }}">
                            <div class="accordion-body pt-0 px-4 pb-3">
                                {!! $item->summary !!}
                            </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>

    @endif

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

    @push('footer')
    <script>
        function faqSearch() {
            var input, filter, ul, li, i, txtValue;
            input = document.getElementById('sss-search');
            filter = input.value.toUpperCase();
            ul = document.getElementById('sss-section');
            li = ul.querySelectorAll('.accordion-item');

            for (i = 0; i < li.length; i++) {
                txtValue = li[i].textContent || li[i].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    li[i].style.display = "";
                } else {
                    li[i].style.display = "none";
                }
            }
        }
    </script>
    @endpush

</x-fe-layout>