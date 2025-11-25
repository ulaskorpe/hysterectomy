<div @class([
    'vstack' => $isMobile,
    'hstack align-items-center justify-content-around' => !$isMobile,
    'gap-2 border rounded p-3'
])>

    @if (isset($settings->contact['phone']) && $settings->contact['phone'] != "")
    <div class="hstack gap-2 align-items-center fw-bold">
        <i class="bi bi-telephone text-primary"></i>
        {{$settings->contact['phone']}}
    </div>
    @endif

    @if (isset($settings->contact['email']) && $settings->contact['email'] != "")
    <div class="hstack gap-2 align-items-center fw-bold">
        <i class="bi bi-envelope-at text-primary"></i>
        {{$settings->contact['email']}}
    </div>
    @endif

    @if ($contactForm)
    <x-form-component :form="$contactForm" />
    @endif

</div>