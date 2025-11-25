<x-fe-layout :header-layout="$content->header_layout">

    @if (data_get($content->additional, 'hideHeader') === false)
    <x-page-header :content="$content"/>
    @endif
    
    @php
        $layout = $content->layout ?? $content->content_type->layout;
    @endphp
    
    <div @class([
        'custom-layout',
        'container' => !$layout->full_width,
        'sidebar-left' => $layout->left_sidebar && !$layout->right_sidebar,
        'sidebar-right' => !$layout->left_sidebar && $layout->right_sidebar,
        'sidebar-both' => $layout->left_sidebar && $layout->right_sidebar,
    ])>

        @if ($layout->left_sidebar && !$isMobile)
            <div class="sidebar left-sidebar">
                <div class="sticky-top">

                    @if ($layout->left_sidebar_details && $layout->left_sidebar_details->content)
                    @foreach ($layout->left_sidebar_details->content[0]['containers'][0]['rows'][0]['columns'] as $column)
                    <x-column :column="$column" :content="$content" :is-layout="true"/>
                    @endforeach
                    @endif

                </div>
            </div>
        @endif

        <div @class(['custom-layout-content'])>
            
            @foreach ($layout->content as $section)
            <x-section :section="$section" :content="$content"/>
            @endforeach

        </div>

        @if ($layout->right_sidebar)
            <div class="sidebar">
                <div class="sticky-top">

                    @if ($layout->right_sidebar_details && $layout->right_sidebar_details->content)
                    @foreach ($layout->right_sidebar_details->content[0]['containers'][0]['rows'][0]['columns'] as $column)
                    <x-column :column="$column" :content="$content" :is-layout="true"/>
                    @endforeach
                    @endif

                </div>
            </div>
        @endif

    </div>


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