@php

$refund_page = null;
$payment_page = null;

if($settings->refund_page){
    $refund_page = \App\Models\Content::where('uuid',$settings->refund_page)->first();
}

if($settings->payment_delivery_page){
    $payment_page = \App\Models\Content::where('uuid',$settings->payment_delivery_page)->first();
}

@endphp


@if ($product->description || $refund_page || $payment_page)

<ul class="nav nav-tabs border-bottom-0 gap-2 nav-justified" id="product-tabs" role="tablist">

    @if ($product->description)
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="product-tab-1" data-bs-toggle="tab" data-bs-target="#product-tab-1-pane"
            type="button" role="tab" aria-controls="product-tab-1-pane" aria-selected="true">Ürün Açıklaması</button>
    </li>
    @endif
    
    @if ($refund_page)
    <li class="nav-item" role="presentation">
        <button @class(['nav-link','active' => !$product->description]) id="product-tab-3" data-bs-toggle="tab" data-bs-target="#product-tab-3-pane"
            type="button" role="tab" aria-controls="product-tab-3-pane" aria-selected="false">İade & Değişim
            İşlemleri</button>
    </li>
    @endif

    @if ($payment_page)
    <li class="nav-item" role="presentation">
        <button @class(['nav-link','active' => !$product->description && !$payment_page]) id="product-tab-4" data-bs-toggle="tab" data-bs-target="#product-tab-4-pane"
            type="button" role="tab" aria-controls="product-tab-4-pane" aria-selected="false">Ödeme Seçenekleri</button>
    </li>
    @endif
</ul>

<div class="border rounded-bottom px-3 px-xl-4 pt-4 pb-3 container-p-0">
    <div class="tab-content" id="myTabContent">

        @if ($product->description)
        <div class="tab-pane fade show active" id="product-tab-1-pane" role="tabpanel" aria-labelledby="product-tab-1"
            tabindex="0">
            {!! $product->description !!}
        </div>
        @endif
        
        @if ($refund_page)
        <div @class(['tab-pane fade','show active' => !$product->description]) class="tab-pane fade" id="product-tab-3-pane" role="tabpanel" aria-labelledby="product-tab-3" tabindex="0">
            @if ($refund_page->content)
            @foreach ($refund_page->content as $section)
            <x-section :section="$section" />
            @endforeach
            @endif
        </div>
        @endif

        @if ($payment_page)
        <div @class(['tab-pane fade','show active' => !$product->description && !$payment_page]) class="tab-pane fade" id="product-tab-4-pane" role="tabpanel" aria-labelledby="product-tab-4" tabindex="0">
            @if ($payment_page->content)
            @foreach ($payment_page->content as $section)
            <x-section :section="$section" />
            @endforeach
            @endif
        </div>
        @endif

    </div>
</div>

@endif