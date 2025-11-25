@php
    $navbarClasses = 'navbar cs-navbar shadow-sm';
    $linkClasses = 'nav-link';
    $buttonOneClass = 'btn btn-sm py-1 px-5 btn-primary';
    $buttonTwoClass = 'btn btn-sm py-1 px-5 btn-primary';
    $logo = isset($settings->logo['main']) ? $settings->logo['main'] : null;
    $logoMobile = isset($settings->logo['mobile']) ? $settings->logo['mobile'] : null;

    if( $headerLayout ){

        if( isset($headerLayout['json_data']['backgroundClass']) && !empty($headerLayout['json_data']['backgroundClass']) ){
            $navbarClasses = 'navbar cs-navbar py-0 '.$headerLayout['json_data']['backgroundClass'];
        }

        if( isset($headerLayout['json_data']['linkColorClass']) && !empty($headerLayout['json_data']['linkColorClass']) ){
            $linkClasses = 'nav-link '.$headerLayout['json_data']['linkColorClass'];
        }

        if( isset($headerLayout['json_data']['headerButtonOneClass']) && !empty($headerLayout['json_data']['headerButtonOneClass']) ){
            $buttonOneClass = $headerLayout['json_data']['headerButtonOneClass'];
        }

        if( isset($headerLayout['json_data']['headerButtonTwoClass']) && !empty($headerLayout['json_data']['headerButtonTwoClass']) ){
            $buttonTwoClass = $headerLayout['json_data']['headerButtonTwoClass'];
        }

        if( isset($headerLayout['json_data']['logo']['main']) && !empty($headerLayout['json_data']['logo']['main']) ){
            $logo = $headerLayout['json_data']['logo']['main'];
        }

        if( isset($headerLayout['json_data']['logo']['mobile']) && !empty($headerLayout['json_data']['logo']['mobile']) ){
            $logoMobile = $headerLayout['json_data']['logo']['mobile'];
        }

    }

    $logoLink = '/';
    if( app()->getLocale() != config('languages.default') ){
        $logoLink = '/'.app()->getLocale();
    }

@endphp

@if ($isMobile)

    <header @class([$navbarClasses]) id="main-mobile-navbar">

        <div class="d-flex w-100 px-3 align-items-center" aria-label="Top Navigation">

            <a href="{{$logoLink}}" class="logo mw-150px">
                @if ($logoMobile)
                <img class="img-fluid" fetchpriority="high" src="{{ $logoMobile['original_url'] }}" alt="{{ $settings->site_name }}" width="{{ $logoMobile['custom_properties']['width'] }}" height="{{ $logoMobile['custom_properties']['height'] }}">
                @elseif ($logo)
                <img class="img-fluid" fetchpriority="high" src="{{ $logo['original_url'] }}" alt="{{ $settings->site_name }}" width="{{ $logo['custom_properties']['width'] }}" height="{{ $logo['custom_properties']['height'] }}">
                @else
                <span>{{ $settings->site_name }}</span>
                @endif
            </a>

            <div class="d-flex ms-auto align-items-center gap-3">
                <button class="btn text-white p-0 align-items-center justify-content-center" type="button" data-bs-toggle="offcanvas" data-bs-target="#csNavbar" aria-controls="csNavbar" aria-label="Toggle navigation">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" class="bi" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M2.5 11.5A.5.5 0 0 1 3 11h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4A.5.5 0 0 1 3 7h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4A.5.5 0 0 1 3 3h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"></path>
                    </svg>
                </button>
            </div>

        </div>

    </header>

@else

    <div class="topbar bg-theme-3">
        <div class="container large-container d-flex flex-row justify-content-between">

            <x-social-media-accounts classes="text-white"/>

            @if (isset($settings->header_buttons[app()->getLocale()]))
            <div class="d-flex align-items-center gap-2">
                @if ($settings->header_buttons[app()->getLocale()]['button_1']['active'])
                <a href="{{$settings->header_buttons[app()->getLocale()]['button_1']['button_link']}}" @class([$buttonOneClass]) @if ($settings->header_buttons[app()->getLocale()]['button_1']['new_window']) target="_blank" @endif>
                    {!! $settings->header_buttons[app()->getLocale()]['button_1']['button_text'] !!}
                </a>
                @endif
                @if ($settings->header_buttons[app()->getLocale()]['button_2']['active'])
                <a href="{{$settings->header_buttons[app()->getLocale()]['button_2']['button_link']}}" @class([$buttonTwoClass]) @if ($settings->header_buttons[app()->getLocale()]['button_2']['new_window']) target="_blank" @endif>
                    {!! $settings->header_buttons[app()->getLocale()]['button_2']['button_text'] !!}
                </a>
                @endif
            </div>
            @endif

            @if ($settings->contact['phone'])
            <div class="d-flex align-items-center gap-2 text-light">
                <i class="bi bi-telephone-fill rotate-270"></i>
                <small class="lh-1">{{ $settings->contact['phone'] }}</small>
            </div>
            @endif

        </div>
    </div>

    <header @class([$navbarClasses]) id="main-navbar">
        
        <div class="container large-container">

            <div class="d-flex w-100 align-items-center gap-3 position-relative">

                <a href="{{$logoLink}}" class="logo">
                    @if ($logo)
                    <img fetchpriority="high"  src="{{ $logo['original_url'] }}" alt="{{ $settings->site_name }}" width="{{ $logo['custom_properties']['width'] }}" height="{{ $logo['custom_properties']['height'] }}">
                    @else
                    <span>{{ $settings->site_name }}</span>
                    @endif
                </a>

                <button class="d-xl-none btn text-white text-hover-primary p-0 border-0 ms-auto" type="button" data-bs-toggle="offcanvas" data-bs-target="#csNavbar" aria-controls="csNavbar" aria-label="Toggle navigation">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" class="bi" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M2.5 11.5A.5.5 0 0 1 3 11h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4A.5.5 0 0 1 3 7h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4A.5.5 0 0 1 3 3h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"></path>
                    </svg>
                </button>

                <nav class="d-none d-xl-flex align-items-center ms-auto" aria-label="Main navigation">
                    @if (session()->has('core_menus') && isset(session()->get('core_menus')['data'][app()->getLocale()]['main']))
        
                        <ul class="navbar-nav d-flex flex-row flex-nowrap align-items-center">
                            @foreach (session()->get('core_menus')['data'][app()->getLocale()]['main']['items'] as $item)
                            @if (count($item['child_nodes']) > 0)
                            <li @class(['nav-item dropdown','ktm-mega-menu'=> $item['megamenu']])>
                                <a href="#" @class([$linkClasses,'dropdown-toggle','pe-0' => $loop->last,'active' => isActiveMenu($item)]) data-bs-toggle="dropdown" aria-expanded="false">{!! $item['menu_title'] !!}</a>
                                @if ($item['megamenu'])
                                <div class="dropdown-menu mega-menu">
                                    <nav class="container">
                                        <div class="mega-menu-content py-5 px-5">
                                            <ul class="row row-cols-2 row-cols-lg-4 row-cols-xl-5 g-4 list-unstyled ps-0">
                                                @foreach ($item['child_nodes'] as $sub)
                                                <li class="col">
                                                    <a class="vstack gap-2 text-decoration-none" href="{{$sub['item_link']}}">
                                                        @if ($sub['image'])
                                                        <div class="overflow-hidden h-150px hover-image-scale">
                                                            <img loading="lazy" src="{{ $sub['image'] }}" alt="{{ $sub['menu_title'] }}" class="w-100 h-100 object-fit-cover">
                                                        </div>
                                                        @endif
                                                        <span class="fw-bolder">{!! $sub['menu_title'] !!}</span>
                                                        @if (isset($sub['item_desc']) && !empty($sub['item_desc']))
                                                        <small class="text-primary">{{$sub['item_desc']}}</small>
                                                        @endif
                                                    </a>
                                                    @if ($sub['child_nodes'])
                                                    <ul class="list-unstyled mt-2 mb-0 vstack gap-2">
                                                        @foreach ($sub['child_nodes'] as $third)
                                                        <li>
                                                            <a href="{{ $third['item_link'] }}" class="link-dark link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover">{!! $third['menu_title'] !!}</a>
                                                        </li>
                                                        @endforeach
                                                    </ul>
                                                    @endif
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </nav>
                                </div>
                                @else
                                <ul class="dropdown-menu">
                                    @foreach ($item['child_nodes'] as $sub)
                                    <li>
                                        <a href="{{$sub['item_link']}}" class="dropdown-item">{!! $sub['menu_title'] !!}</a>
                                    </li>
                                    @endforeach
                                </ul>
                                @endif
                            </li>
                            @else
                            <li @class(['nav-item'])>
                                <a href="{{$item['item_link']}}" @class([$linkClasses,'pe-0' => $loop->last,'active' => isActiveMenu($item)])>{!! $item['menu_title'] !!}</a>
                            </li>
                            @endif
                            @endforeach

                        </ul>
        
                    @endif
                </nav>

                
                @if (config('languages.language_bar'))
                <ul class="navbar-nav language-switcher">
                    <li class="nav-item dropdown ms-auto">
                        <a href="#" @class([$linkClasses,'dropdown-toggle','has-caret','px-0','language-toggle']) data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="fs-xs">{{ Str::upper(app()->getLocale()) }}</span>
                        </a>
                        <x-switch-language />
                    </li>
                </ul>
                @endif

            </div>

        </div>

    </header>

@endif

{{-- offcanvas ekran boyutuna gore desktop icin de kullanılabilir. --}}

<div @class(['offcanvas offcanvas-end bg-theme-3']) tabindex="-1" id="csNavbar" data-bs-scroll="false">
    <div class="offcanvas-header px-3 py-3">
        <button type="button" class="btn text-white p-0 border-0 shadow-none bg-transparent ms-auto" data-bs-dismiss="offcanvas" aria-label="Close" data-bs-target="#csNavbar"><i class="bi bi-x-lg fs-3"></i></button>
    </div>
    <div class="offcanvas-body d-flex flex-column">

        @if (session()->has('core_menus') && isset(session()->get('core_menus')['data'][app()->getLocale()]['main']))
        <nav>
            <ul class="list-unstyled d-flex flex-column gap-3">
                @foreach (session()->get('core_menus')['data'][app()->getLocale()]['main']['items'] as $item)
                <li>
                    <a href="{{$item['item_link']}}" @class(['main-level link-light text-decoration-none'])>{!! $item['menu_title'] !!}</a>
                    @if (count($item['child_nodes']) > 0)
                    <ul class="list-unstyled d-flex flex-column gap-1 mb-2 mt-1">
                        @foreach ($item['child_nodes'] as $sub)
                        <li>
                            <a href="{{$sub['item_link']}}" class="text-light text-decoration-none fw-semibold">{!! $sub['menu_title'] !!}</a>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </li>
                @endforeach
            </ul>
        </nav>
        @endif

        @if (config('languages.language_bar'))
        <div class="mt-5">
            <x-switch-language />
        </div>
        @endif

        <div>
            @if (isset($settings->header_buttons[app()->getLocale()]))
                <div class="vstack gap-2 mt-5">
                    @if ($settings->header_buttons[app()->getLocale()]['button_1']['active'])
                    <a href="{{$settings->header_buttons[app()->getLocale()]['button_1']['button_link']}}" @class(['btn btn-sm',$buttonOneClass]) @if ($settings->header_buttons[app()->getLocale()]['button_1']['new_window']) target="_blank" @endif>
                        {!! $settings->header_buttons[app()->getLocale()]['button_1']['button_text'] !!}
                    </a>
                    @endif
                    @if ($settings->header_buttons[app()->getLocale()]['button_2']['active'])
                    <a href="{{$settings->header_buttons[app()->getLocale()]['button_2']['button_link']}}" @class(['btn btn-sm',$buttonTwoClass]) @if ($settings->header_buttons[app()->getLocale()]['button_2']['new_window']) target="_blank" @endif>
                        {!! $settings->header_buttons[app()->getLocale()]['button_2']['button_text'] !!}
                    </a>
                    @endif

                </div>
            @endif
        </div>

        <div class="text-white d-flex flex-column gap-2 mt-5">
            @if ($settings->contact['address'])
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-geo-alt"></i>
                <span>{!! $settings->contact['address'] !!}</span>
            </div>
            @endif
        </div>

    </div>
</div>