<div class="product-card rounded-3 overflow-hidden h-100 bg-light p-3">

    @if ($popup || $product->product_type->popup_products)

    <div class="cursor-pointer" data-product-title="{{$product->name}}" data-product-uuid="{{$product->uuid}}" onclick="openOffCanvasProduct(this);">
        <h3 class="fw-semibold h6 mb-3 pb-2 border-bottom text-dark d-flex justify-content-between align-items-center">
            <span>{{$product->name}}</span>
            <i class="bi bi-arrow-right"></i>
        </h3>
        <x-product-price :product="$product" :popup="$popup ?? false"/>

        @if (count($product->attributes) > 0)
        <div class="border rounded bg-white p-3 mt-2">
            <x-product-layout.product-layout-attributes :product-attributes="$product->attributes" />
        </div>
        @endif
    </div>

    @else

    <a href="{{ $product->uri->final_uri }}" class="d-block text-decoration-none text-reset">
        <h3 class="fw-semibold h6 mb-3 pb-2 border-bottom text-dark d-flex justify-content-between align-items-center">
            <span>{{$product->name}}</span>
            <i class="bi bi-arrow-right"></i>
        </h3>
        <x-product-price :product="$product" :popup="$popup ?? false"/>

        @if (count($product->attributes) > 0)
        <div class="border rounded bg-white p-3 mt-2">
            <x-product-layout.product-layout-attributes :product-attributes="$product->attributes" />
        </div>
        @endif
    </a>

    @endif
    
</div>