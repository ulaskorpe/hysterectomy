@php
    $use_quantity = false;
    if( $product->product_variants->count() > 0 && $product->product_type->option_group && $product->product_type->option_group->stock_usage ){
        $use_quantity = true;
    }
@endphp

<section @class(['content-section','pt-0' => $popup, 'pb-0' => count($product->content) > 0])>
    <div class="container">
        <div class="row g-0 justify-content-center">

            <div class="col-xl-7 d-flex flex-column gap-4 ">

                <x-product-layout.product-layout-name :name="$product->name" :id="$product->id"/>

                @if (count($product->attributes) > 0)
                <div class="p-3 rounded border bg-gray-50">
                    <x-product-layout.product-layout-attributes :product-attributes="$product->attributes" />
                </div>
                @endif
                
                @if ($product->summary)
                {!! $product->summary !!}
                @endif

                <x-product-layout.product-layout-price :product="$product" />

                <x-product-layout.product-layout-basket :product="$product" :use-quantity="$use_quantity"/>

            </div>

        </div>
    </div>

</section>

@if ($product->content && count($product->content) > 0)
@foreach ($product->content as $section)
<x-section :section="$section"/>
@endforeach
@endif

