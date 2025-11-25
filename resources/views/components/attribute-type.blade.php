@if ($option['option_type'] == 'select')
    @php
        $filtered = Arr::first($option['values'], function ($value, $key) use($option) {
            return $value['id'] === $option['value'];
        });
    @endphp
    @if ($filtered)
    <dl class="row mb-0">
        <dt class="col-6">{{$option['name']}}</dt>
        <dd class="col-6 mb-0  text-end">{{ $filtered['name'] }}</dd>
    </dl>
    @endif
@endif

@if ($option['option_type'] == 'time')
    <dl class="row mb-0">
        <dt class="col-6">{{$option['name']}}</dt>
        <dd class="col-6 mb-0 text-end">{{ \Carbon\Carbon::parse($option['value'])->format('H:i') }}</dd>
    </dl>
@endif

@if ($option['option_type'] == 'date')
    <dl class="row mb-0">
        <dt class="col-6">{{$option['name']}}</dt>
        <dd class="col-6 mb-0 text-end">{{ \Carbon\Carbon::parse($option['value'])->format('d.m.Y') }}</dd>
    </dl>
@endif

@if ($option['option_type'] == 'vimeo' && $option['value'] != '')
<div class="ratio ratio-16x9 rounded overflow-hidden mb-3">
    <iframe src="https://player.vimeo.com/video/{{$option['value']}}" title="Vimeo video" allowfullscreen></iframe>
</div>
@endif

@if ($option['option_type'] == 'youtube' && $option['value'] != '')
<div class="ratio ratio-16x9 rounded overflow-hidden mb-3">
    <iframe src="https://www.youtube.com/embed/{{$option['value']}}" title="Yotube video" allowfullscreen></iframe>
</div>
@endif

@if (in_array($option['option_type'],['textarea','input']))
    <dl class="row mb-0">
        <dt class="col-4">{{$option['name']}}</dt>
        <dd class="col-8 mb-0 text-end">{{ $option['value'] }}</dd>
    </dl>
@endif

@if ($option['option_type'] == 'file')
    <dl class="row mb-0">
        <dt class="col-6">{{$option['name']}}</dt>
        <dd class="col-6 mb-0">
            <a href="{{$option['value']}}" target="_blank" class="d-flex align-items-center link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover">Görüntüle <i class="bi bi-file-pdf ms-2"></i></a>
        </dd>
    </dl>
@endif

@if ($option['option_type'] == 'image')
    <img src="{{$option['value']}}" class="img-fluid rounded" loading="lazy"/>
@endif