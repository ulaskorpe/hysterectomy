<div class="text-center" id="product-{{$product->uuid}}-buttons">

    @if ($product->product_type->is_cartable)

        @if ($product->use_option_group && $product->product_type->option_group && $product->product_type->option_group->has_own_price)
            
            @if ($popup || $product->product_type->popup_products)
            <div class="btn btn-sm btn-primary rounded-0 w-100" data-product-title="{{$product->name}}" data-product-uuid="{{$product->uuid}}" onclick="openOffCanvasProduct(this);">
                <i class="bi bi-cart"></i> {{__('Detaylar')}}
            </div>
            @else
            <a href="{{$product->uri->final_uri}}" class="btn btn-sm btn-primary rounded-0 w-100">
                <i class="bi bi-cart"></i> {{__('Detaylar')}}
            </a>
            @endif

        @else
            <form>
                <input type="hidden" name="product_id" value="{{$product->id}}"/>

                @if ($product->incart['in_cart'])
                <div class="h-50px bg-white rounded-pill border border-secondary hstack gap-2 align-items-center justify-content-between px-3">
                    @if ($product->incart['quantity'] == 1)
                    <span class="text-secondary cursor-pointer" onclick="removeItemFromCart('{{$product->incart['hash']}}')">
                        <i class="bi bi-trash fs-6"></i>
                    </span>
                    @endif
                    @if ($product->incart['quantity'] > 1)
                    <span class="text-secondary cursor-pointer" onclick="changeItemQuantity('{{$product->incart['hash']}}',{{ $product->incart['quantity'] - 1 }})">
                        <i class="bi bi-dash fs-4"></i>
                    </span>
                    @endif
                    <small class="px-1"><b>{{ $product->incart['quantity'] }} Adet</b></small>
                    <span class="text-secondary cursor-pointer" onclick="changeItemQuantity('{{$product->incart['hash']}}',{{ $product->incart['quantity'] + 1 }})">
                        <i class="bi bi-plus fs-4"></i>
                    </span>
                </div>
                @else
                <input type="hidden" name="quantity" value="1"/>
                <button onclick="addToCart(this);" type="button" class="btn btn-secondary box box-50 bg-secondary d-flex align-items-center justify-content-center rounded-circle text-white">
                    <i class="bi bi-plus fs-1"></i>
                </button>
                @endif
            </form>

        @endif

    @else

    <a href="{{$product->uri->final_uri}}" class="btn btn-sm btn-primary rounded-0 w-100">
        {{__('Detaylar')}}
    </a>

    @endif

</div>