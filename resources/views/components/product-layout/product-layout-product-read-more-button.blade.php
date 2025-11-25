
@if ($popup)
    <div class="cursor-pointer" data-product-title="{{$product->name}}" data-product-uuid="{{$product->uuid}}" onclick="openOffCanvasProduct(this);">
        {!! Str::replace('--LINK--', 'javascript:void(0);', $element['data']['elemHtml']) !!}
    </div>
@else
    <div>
        {!! Str::replace('--LINK--', $product->uri->final_uri, $element['data']['elemHtml']) !!}
    </div>
@endif