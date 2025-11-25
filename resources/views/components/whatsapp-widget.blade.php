@if ($settings->contact['whatsapp'])

    <a href="https://wa.me/{{ Str::replace(['+',' ','(',')'],'',$settings->contact['whatsapp']) }}" target="_blank" @class([
        'position-fixed zindex-3 end-0 bottom-0 m-4 animate__animated animate__zoomIn delay-1000ms'
    ])>
        <img src="/images/whatsapp.svg" alt="Whatsapp" width="50" height="50">
    </a>

@endif