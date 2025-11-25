@php
    
    //aktif kupon varsa kampanya alanini gostermeyecegiz.
    $usable_campaigns = [];
    $show_campaign_module = true;
    $active_campaign = null;

    $active_coupon = Arr::first($cart->get('applied_actions')->toArray(), function ($value, $key) {
        return $value['group'] === "CouponDiscount";
    });

    if( $active_coupon ){

        $show_campaign_module = false;
        
    } else {

        $campaigns = \App\Models\Campaign::with(['main_image','product_types:id,name','uri'])->get();

        foreach ($campaigns as $key => $campaign) {
            if( $campaign->isUsable() ){
                $usable_campaigns[] = $campaign;
            }
        }

        $active_campaign = Arr::first($cart->get('applied_actions')->toArray(), function ($value, $key) {
            return $value['group'] === "CampaignDiscount";
        });
    }

@endphp

@if ($show_campaign_module && count($usable_campaigns) > 0)

    <div @class(['d-flex flex-column fs-xs mb-3','p-3 rounded bg-opacity-10 bg-success border border-success gap-3' => $active_campaign])>
        @if ($active_campaign)
        <div class="d-flex align-items-center gap-2">
            <form class="needs-validation" novalidate method="POST" action="{{route('cart.remove-campaign')}}" onsubmit="FreezeUI({text:' '});this.querySelector('[type=submit]').disabled=true;">
                @csrf
                @honeypot
                <input type="hidden" class="form-control" name="hash" value="{{$active_campaign['hash']}}">
                <button class="btn btn-sm text-danger p-0 border-0" type="submit" data-bs-toggle="tooltip" title="Çıkar"><i class="bi bi-x-lg"></i></button>
            </form>
            <span>{{$active_campaign['title']}}</span>
            <span class="fw-bold ms-auto">{!! formatNumberToCurrency($active_campaign['amount']) !!}</span>
        </div>
        @endif
        <button class="btn btn-success btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#campaigns-offcanvas" aria-controls="campaigns-offcanvas">
            {{ __('Kampanyalar') }}
        </button>
    </div>

    @push('footer')
    <div class="offcanvas offcanvas-size-xxl offcanvas-end border-0" tabindex="-1" id="campaigns-offcanvas">
        <div class="offcanvas-header bg-dark border-0 text-white">
            <span class="offcanvas-title">Yararlanabileceğiniz Kampanyalar</span>
            <button type="button" class="btn btn-bg-primary text-white px-2 py-0" data-bs-dismiss="offcanvas" aria-label="Close"><i class="bi bi-x fs-4"></i></button>
        </div>
        <div class="offcanvas-body">
            @foreach ($usable_campaigns as $campaign)
            <div class="p-3 rounded mb-3 d-flex flex-row border bg-light">
                @if (isset($campaign->main_image[0]))
                    <img src="{{$campaign->main_image[0]->preview_url}}" alt="{{$campaign->name}}" width="75" height="75" class="me-3">
                @endif
                <div class="w-100">
                    <h6 class="mb-2 fw-semibold">{{$campaign->name}}</h6>
                    @if ($campaign->summary)
                        {!!$campaign->summary!!}
                    @endif
                    <div class="d-flex align-items-center justify-content-between">
                        <a href="{{$campaign->uri->final_uri}}" target="_blank">Detaylı Bilgi</a>
                        <form class="needs-validation" novalidate="" method="POST" action="{{route('cart.apply-campaign')}}" onsubmit="this.querySelector('[type=submit]').disabled=true;">
                            @csrf
                            @honeypot
                            <input type="hidden" name="campaign_id" value="{{$campaign->id}}">
                            <button class="btn btn-success btn-sm" type="submit">Uygula</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endpush

@endif