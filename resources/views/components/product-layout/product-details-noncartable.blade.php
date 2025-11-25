@php
    $use_quantity = true;
    if( $product->product_variants->count() > 0 && $product->product_type->option_group->stock_usage ){
        $use_quantity = true;
    }
@endphp

<section class="pt-0">

<div class="container">

    <div class="">
        <div class="row g-4 g-xl-5">
            
            @if ($product->media_objects['mainImage'])
            <div class="col-lg-5 col-xl-4">
                <div class="rounded d-flex align-items-center overflow-hidden">
                    <x-product-layout.product-layout-images :images="$product->media_objects" :name="$product->name" />
                </div>
            </div>
            @endif

            <div @class(['col-lg-7 col-xl-8'])>

                <div class="mb-4">

                    <x-product-layout.product-layout-name :name="$product->name" :id="$product->id" />
                    
                    <hr>
                    
                    @if ($product->summary)
                    {!! $product->summary !!}
                    @endif

                    <x-product-layout.product-layout-event-date-time-location :product="$product"/>

                    @if (count($product->attributes) > 0)
                    <x-product-layout.product-layout-attributes :productAttributes="$product->attributes" />
                    @endif

                    @if ($product->product_type->is_contactable)
                    <div class="mt-4">
                        <x-product-layout.product-layout-contact :contact-form="$product->product_type->contact_form"/>
                    </div>
                    @endif

                </div>

            </div>


        </div>
    </div>
    
    @if ($product->description)
    <div class="rounded p-3 p-xl-4 border mt-4 bg-light">
        <h2 class="lead-responsive">Detaylı Bilgi</h2>
        {!! $product->description !!}
    </div>
    @endif

</div>

</section>

@if ($product->content && count($product->content) > 0)
@foreach ($product->content as $section)
<x-section :section="$section"/>
@endforeach
@endif