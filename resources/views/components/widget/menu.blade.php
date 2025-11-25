@php
    
    $selectedMenu = null;
    if( $menu['menu'] ){
        $selectedMenu = \App\Models\Menu::find($menu['menu']['id']);
    }

    $elemDivClasses = ['nav'];

    if( $selectedMenu ){

        if($menu['direction'] == 'vertical'){
            $elemDivClasses[] = 'flex-column';
        }

        if($menu['baseDesignOptions']['class']) $elemDivClasses[] = $menu['baseDesignOptions']['class'];
        if(isset($menu['baseDesignOptions']['position'])) $elemDivClasses[] = $menu['baseDesignOptions']['position'];

        foreach ($menu['baseDesignOptions']['margin'] as $key => $value) {
            if($value) $elemDivClasses[] = $value;
        }
        foreach ($menu['baseDesignOptions']['padding'] as $key => $value) {
            if($value) $elemDivClasses[] = $value;
        }
        
    }

@endphp

@if ($selectedMenu)

@switch($menu['direction'])
    
    @case("horizontal")
    <ul @class($elemDivClasses)>
        @foreach ($selectedMenu->tree as $item)
        <li class="nav-item">
            <a href="{{$item['item_link']}}" class="nav-link fw-semibold">{!! $item['menu_title'] !!}</a>
        </li>
        @endforeach
    </ul>
    @break

    @case("navbar")
    <div class="container">
        <nav class="d-flex align-items-center w-100">
            <ul class="navbar-nav d-none d-xl-flex flex-row flex-nowrap align-items-center justify-content-between w-100">
                @foreach ($selectedMenu->tree as $item)
                @if (count($item['child_nodes']) > 0)
                <li @class(['nav-item dropdown'])>
                    <a href="#" @class(['nav-link','py-3','text-uppercase','dropdown-toggle']) data-bs-toggle="dropdown" aria-expanded="false">{!! $item['menu_title'] !!}</a>
                    <ul class="dropdown-menu mt-n1 border-0 py-0 bg-light">
                        @foreach ($item['child_nodes'] as $sub)
                        <li>
                            <a href="{{$sub['item_link']}}" class="dropdown-item py-3 text-uppercase">{!! $sub['menu_title'] !!}</a>
                        </li>
                        @endforeach
                    </ul>
                </li>
                @else
                <li class="nav-item">
                    <a href="{{$item['item_link']}}" @class(['nav-link','py-3','text-uppercase'])>{!! $item['menu_title'] !!}</a>
                </li>
                @endif
                @endforeach
            </ul>
        </nav>
    </div>
    @break
    
    @default

    @if ($menu['accordeon'])
        @php
            $menuAccId = 'menu-'.Str::uuid();
        @endphp
        <div class="accordion vstack" id="{{$menuAccId}}">
            @foreach ($selectedMenu->tree as $item)
                @php
                    $menuItemUuid = Str::uuid();
                @endphp

                @if (!empty($item['child_nodes']))
                    
                <div class="accordion-item rounded-0 border-0 border-top border-gray py-1">
                    <div class="accordion-header border-0">
                    <button @class([
                        'accordion-button rounded-0 fw-bolder bg-transparent px-0 py-2',
                        'collapsed' => !isActiveMenu($item)
                    ]) type="button" data-bs-toggle="collapse" data-bs-target="#menu-collapse-{{$menuItemUuid}}" aria-expanded="true" aria-controls="menu-collapse-{{$menuItemUuid}}">
                        {!! $item['menu_title'] !!}
                    </button>
                    </div>
                    <div id="menu-collapse-{{$menuItemUuid}}" data-bs-parent="#{{$menuAccId }}" @class([
                        'accordion-collapse collapse',
                        'show' => isActiveMenu($item)
                    ])>
                        <div class="accordion-body p-0 pb-3">
                            @if ($item['child_nodes'])
                            <ul class="nav flex-column gap-2">
                                @foreach ($item['child_nodes'] as $child)
                                <li class="d-flex align-items-center justify-content-between gap-2">
                                    <a href="{{$child['item_link']}}" class="link-dark link-offset-2 link-underline link-underline-opacity-0 link-underline-opacity-75-hover">{{$child['menu_title']}}</a>
                                    @if (isActiveMenu($child))
                                    <span>
                                        <svg width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M10.3874 10.083C10.3366 10.0524 10.2947 10.0092 10.2656 9.95759C10.2364 9.906 10.2211 9.84775 10.2211 9.78851C10.2211 9.72926 10.2364 9.67102 10.2656 9.61942C10.2947 9.56783 10.3366 9.52463 10.3874 9.49401L17.1234 5.60801L16.7704 4.99701L10.0434 8.88301C9.99803 8.90901 9.95203 8.93334 9.90536 8.95601C9.85678 8.97768 9.80387 8.98789 9.75071 8.98586C9.69756 8.98382 9.64558 8.96959 9.5988 8.94427C9.55202 8.91895 9.51169 8.88321 9.48092 8.83981C9.45015 8.79642 9.42977 8.74653 9.42136 8.69401C9.40135 8.57976 9.39131 8.46399 9.39136 8.34801V0.583008H8.68036V8.34801C8.6805 8.464 8.67046 8.57977 8.65036 8.69401C8.6419 8.7464 8.62153 8.79616 8.59083 8.83945C8.56013 8.88274 8.5199 8.91841 8.47325 8.94371C8.4266 8.96902 8.37476 8.98328 8.32173 8.98542C8.2687 8.98755 8.21589 8.97749 8.16736 8.95601C8.12069 8.93401 8.07469 8.90967 8.02936 8.88301L1.29636 4.99701L0.943359 5.60801L7.67536 9.49501C7.72608 9.52563 7.76803 9.56883 7.79716 9.62042C7.82628 9.67202 7.84158 9.73026 7.84158 9.78951C7.84158 9.84875 7.82628 9.907 7.79716 9.95859C7.76803 10.0102 7.72608 10.0534 7.67536 10.084L0.943359 13.976L1.29636 14.583L8.02836 10.696C8.07369 10.67 8.11969 10.6457 8.16636 10.623C8.21489 10.6015 8.2677 10.5915 8.32073 10.5936C8.37376 10.5957 8.4256 10.61 8.47225 10.6353C8.5189 10.6606 8.55913 10.6963 8.58983 10.7396C8.62053 10.7829 8.6409 10.8326 8.64936 10.885C8.66946 10.9992 8.6795 11.115 8.67936 11.231V18.996H9.38536V11.235C9.38531 11.119 9.39535 11.0033 9.41536 10.889C9.42377 10.8365 9.44415 10.7866 9.47492 10.7432C9.50569 10.6998 9.54602 10.6641 9.5928 10.6387C9.63958 10.6134 9.69156 10.5992 9.74471 10.5972C9.79787 10.5951 9.85078 10.6053 9.89936 10.627C9.94603 10.649 9.99203 10.6733 10.0374 10.7L16.7704 14.583L17.1234 13.972L10.3914 10.089" fill="black"/>
                                            <path d="M10.3874 10.083C10.3366 10.0524 10.2947 10.0092 10.2656 9.95759C10.2364 9.906 10.2211 9.84775 10.2211 9.78851C10.2211 9.72926 10.2364 9.67102 10.2656 9.61942C10.2947 9.56783 10.3366 9.52463 10.3874 9.49401L17.1234 5.60801L16.7704 4.99701L10.0434 8.88301C9.99803 8.90901 9.95203 8.93334 9.90536 8.95601C9.85678 8.97768 9.80387 8.98789 9.75071 8.98586C9.69756 8.98382 9.64558 8.96959 9.5988 8.94427C9.55202 8.91895 9.51169 8.88321 9.48092 8.83981C9.45015 8.79642 9.42977 8.74653 9.42136 8.69401C9.40135 8.57976 9.39131 8.46399 9.39136 8.34801V0.583008H8.68036V8.34801C8.6805 8.464 8.67046 8.57977 8.65036 8.69401C8.6419 8.7464 8.62153 8.79616 8.59083 8.83945C8.56013 8.88274 8.5199 8.91841 8.47325 8.94371C8.4266 8.96902 8.37476 8.98328 8.32173 8.98542C8.2687 8.98755 8.21589 8.97749 8.16736 8.95601C8.12069 8.93401 8.07469 8.90967 8.02936 8.88301L1.29636 4.99701L0.943359 5.60801L7.67536 9.49501C7.72608 9.52563 7.76803 9.56883 7.79716 9.62042C7.82628 9.67202 7.84158 9.73026 7.84158 9.78951C7.84158 9.84875 7.82628 9.907 7.79716 9.95859C7.76803 10.0102 7.72608 10.0534 7.67536 10.084L0.943359 13.976L1.29636 14.583L8.02836 10.696C8.07369 10.67 8.11969 10.6457 8.16636 10.623C8.21489 10.6015 8.2677 10.5915 8.32073 10.5936C8.37376 10.5957 8.4256 10.61 8.47225 10.6353C8.5189 10.6606 8.55913 10.6963 8.58983 10.7396C8.62053 10.7829 8.6409 10.8326 8.64936 10.885C8.66946 10.9992 8.6795 11.115 8.67936 11.231V18.996H9.38536V11.235C9.38531 11.119 9.39535 11.0033 9.41536 10.889C9.42378 10.8365 9.44415 10.7866 9.47492 10.7432C9.50569 10.6998 9.54602 10.6641 9.5928 10.6387C9.63958 10.6134 9.69156 10.5992 9.74471 10.5972C9.79787 10.5951 9.85078 10.6053 9.89936 10.627C9.94603 10.649 9.99203 10.6733 10.0374 10.7L16.7704 14.583L17.1234 13.972L10.3914 10.089" stroke="black" stroke-width="0.1"/>
                                        </svg>
                                    </span>
                                    @endif
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        </div>
                    </div>
                </div>

                @else

                <div class="d-flex align-items-center justify-content-between gap-2 border-top border-gray py-1">
                    <a href="{{$item['item_link']}}" class="fw-bolder py-2 link-dark link-offset-2 link-underline link-underline-opacity-0 link-underline-opacity-75-hover">{{$item['menu_title']}}</a>
                    @if (isActiveMenu($item))
                    <span>
                        <svg width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.3874 10.083C10.3366 10.0524 10.2947 10.0092 10.2656 9.95759C10.2364 9.906 10.2211 9.84775 10.2211 9.78851C10.2211 9.72926 10.2364 9.67102 10.2656 9.61942C10.2947 9.56783 10.3366 9.52463 10.3874 9.49401L17.1234 5.60801L16.7704 4.99701L10.0434 8.88301C9.99803 8.90901 9.95203 8.93334 9.90536 8.95601C9.85678 8.97768 9.80387 8.98789 9.75071 8.98586C9.69756 8.98382 9.64558 8.96959 9.5988 8.94427C9.55202 8.91895 9.51169 8.88321 9.48092 8.83981C9.45015 8.79642 9.42977 8.74653 9.42136 8.69401C9.40135 8.57976 9.39131 8.46399 9.39136 8.34801V0.583008H8.68036V8.34801C8.6805 8.464 8.67046 8.57977 8.65036 8.69401C8.6419 8.7464 8.62153 8.79616 8.59083 8.83945C8.56013 8.88274 8.5199 8.91841 8.47325 8.94371C8.4266 8.96902 8.37476 8.98328 8.32173 8.98542C8.2687 8.98755 8.21589 8.97749 8.16736 8.95601C8.12069 8.93401 8.07469 8.90967 8.02936 8.88301L1.29636 4.99701L0.943359 5.60801L7.67536 9.49501C7.72608 9.52563 7.76803 9.56883 7.79716 9.62042C7.82628 9.67202 7.84158 9.73026 7.84158 9.78951C7.84158 9.84875 7.82628 9.907 7.79716 9.95859C7.76803 10.0102 7.72608 10.0534 7.67536 10.084L0.943359 13.976L1.29636 14.583L8.02836 10.696C8.07369 10.67 8.11969 10.6457 8.16636 10.623C8.21489 10.6015 8.2677 10.5915 8.32073 10.5936C8.37376 10.5957 8.4256 10.61 8.47225 10.6353C8.5189 10.6606 8.55913 10.6963 8.58983 10.7396C8.62053 10.7829 8.6409 10.8326 8.64936 10.885C8.66946 10.9992 8.6795 11.115 8.67936 11.231V18.996H9.38536V11.235C9.38531 11.119 9.39535 11.0033 9.41536 10.889C9.42377 10.8365 9.44415 10.7866 9.47492 10.7432C9.50569 10.6998 9.54602 10.6641 9.5928 10.6387C9.63958 10.6134 9.69156 10.5992 9.74471 10.5972C9.79787 10.5951 9.85078 10.6053 9.89936 10.627C9.94603 10.649 9.99203 10.6733 10.0374 10.7L16.7704 14.583L17.1234 13.972L10.3914 10.089" fill="black"/>
                            <path d="M10.3874 10.083C10.3366 10.0524 10.2947 10.0092 10.2656 9.95759C10.2364 9.906 10.2211 9.84775 10.2211 9.78851C10.2211 9.72926 10.2364 9.67102 10.2656 9.61942C10.2947 9.56783 10.3366 9.52463 10.3874 9.49401L17.1234 5.60801L16.7704 4.99701L10.0434 8.88301C9.99803 8.90901 9.95203 8.93334 9.90536 8.95601C9.85678 8.97768 9.80387 8.98789 9.75071 8.98586C9.69756 8.98382 9.64558 8.96959 9.5988 8.94427C9.55202 8.91895 9.51169 8.88321 9.48092 8.83981C9.45015 8.79642 9.42977 8.74653 9.42136 8.69401C9.40135 8.57976 9.39131 8.46399 9.39136 8.34801V0.583008H8.68036V8.34801C8.6805 8.464 8.67046 8.57977 8.65036 8.69401C8.6419 8.7464 8.62153 8.79616 8.59083 8.83945C8.56013 8.88274 8.5199 8.91841 8.47325 8.94371C8.4266 8.96902 8.37476 8.98328 8.32173 8.98542C8.2687 8.98755 8.21589 8.97749 8.16736 8.95601C8.12069 8.93401 8.07469 8.90967 8.02936 8.88301L1.29636 4.99701L0.943359 5.60801L7.67536 9.49501C7.72608 9.52563 7.76803 9.56883 7.79716 9.62042C7.82628 9.67202 7.84158 9.73026 7.84158 9.78951C7.84158 9.84875 7.82628 9.907 7.79716 9.95859C7.76803 10.0102 7.72608 10.0534 7.67536 10.084L0.943359 13.976L1.29636 14.583L8.02836 10.696C8.07369 10.67 8.11969 10.6457 8.16636 10.623C8.21489 10.6015 8.2677 10.5915 8.32073 10.5936C8.37376 10.5957 8.4256 10.61 8.47225 10.6353C8.5189 10.6606 8.55913 10.6963 8.58983 10.7396C8.62053 10.7829 8.6409 10.8326 8.64936 10.885C8.66946 10.9992 8.6795 11.115 8.67936 11.231V18.996H9.38536V11.235C9.38531 11.119 9.39535 11.0033 9.41536 10.889C9.42378 10.8365 9.44415 10.7866 9.47492 10.7432C9.50569 10.6998 9.54602 10.6641 9.5928 10.6387C9.63958 10.6134 9.69156 10.5992 9.74471 10.5972C9.79787 10.5951 9.85078 10.6053 9.89936 10.627C9.94603 10.649 9.99203 10.6733 10.0374 10.7L16.7704 14.583L17.1234 13.972L10.3914 10.089" stroke="black" stroke-width="0.1"/>
                        </svg>
                    </span>
                    @endif
                </div>

                @endif
            @endforeach
        </div>

        @else

        <ul @class($elemDivClasses)>
            @foreach ($selectedMenu->tree as $item)
            <li class="nav-item py-1 mb-2 text-center border-bottom border-dark border-1">
                <a href="{{$item['item_link']}}" class="fw-bold font-title text-dark text-uppercase text-decoration-none fs-lg">{!! $item['menu_title'] !!}</a>
                @if ($item['child_nodes'])
                <ul class="nav flex-column mt-1 mb-2">
                    @foreach ($item['child_nodes'] as $child)
                    <li>
                        <a href="{{$child['item_link']}}" class="fs-sm link-dark py-1 link-offset-2 link-underline link-underline-opacity-0 link-underline-opacity-75-hover">{{$child['menu_title']}}</a>
                    </li>
                    @endforeach
                </ul>
                @endif
            </li>
            @endforeach
        </ul>

    @endif
        
@endswitch

@endif