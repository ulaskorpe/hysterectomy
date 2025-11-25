            @if (isset($element['layout']) && $element['layout'])

                @switch($element['type'])
                    
                    @case("layout_content")
                        <x-layout-elements.contents :content="$content" />
                    @break

                    @case("layout_title")
                        <x-layout-elements.title :title="$content->name" :element="$element" />
                    @break

                    @case("layout_depending_title")
                    @if ($content->depending_content)
                    <x-layout-elements.title :title="$content->depending_content->name" :element="$element" />
                    @endif
                    @break

                    @case("layout_depending_contents")
                    @if ($content->depending_contents->count() > 0)
                    <x-layout-elements.depending-contents :depending-contents="$content->depending_contents" :element="$element" />
                    @endif
                    @break

                    @case("layout_read_more_button")
                        <x-layout-elements.read-more-button :uri="$content->uri->final_uri ?? '#'" :element="$element" :content-attributes="$content->attributes" />
                    @break

                    @case("layout_meta_data")
                        <x-layout-elements.meta-data :content="$content" :element="$element" />
                    @break

                    @case("layout_summary")
                        <div class="summary">
                        <x-layout-elements.summary :summary="$content->summary" :element="$element" />
                        </div>
                    @break

                    @case("layout_content_attributes")
                        <x-layout-elements.content-attributes :content="$content" :element="$element"/>
                    @break

                    @case("layout_description")
                        <x-layout-elements.description :description="$content->description" :element="$element" />
                    @break

                    @case("layout_mainimage")
                    @if (isset($content->media_objects['mainImage']))
                    <x-layout-elements.main-image :media="$content->media_objects['mainImage']" :element="$element" :alt="$content->name" :content-attributes="$content->attributes ?? []"/>
                    @endif
                    @break

                    @case("layout_gallery_images")
                    @if ($content->gallery_images->count() > 0)
                    <x-layout-elements.gallery-images :gallery-images="$content->gallery_images" :element="$element"/>
                    @endif
                    @break

                    @case("layout_icon")
                    @if (isset($content->additional['icon']) && !empty($content->additional['icon']))
                    <x-layout-elements.content-icon :icon="$content->additional['icon']" :element="$element"/>
                    @endif
                    @break

                    @default
                        
                @endswitch

            @elseif (isset($element['widget']) && $element['widget'])

                <x-widget.widget :widget="$element" :content="$content"/>

            @elseif (isset($element['render_blade']) && $element['render_blade'])
                
                <x-elements.index :element="$element" :content="$content"/>
            
            @else

                {!! generateElementHtml($element['data']['elemHtml'], $content) !!}

            @endif