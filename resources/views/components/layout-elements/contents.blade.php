@if ($content && $content->content && count($content->content) > 0)
    @foreach ($content->content as $section)
    <x-section :section="$section" :content="$content"/>
    @endforeach
@endif