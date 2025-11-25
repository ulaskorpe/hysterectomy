@php
        
    $useStock = $product->product_type->stock_usage;
    $maxQuantity = 100;

    if( $useStock){

        if ($product->incart['in_cart']) {
            $maxQuantity = $product->incart['active_stock'];
        } else {
            $maxQuantity = $product->stock;
        }
    }

@endphp

<div class="mb-4">

    @if ($product->incart['in_cart'] && $maxQuantity <= 0)

        <div data-bs-toggle="tooltip" title="{{__('Bu üründen alabileceğiniz maksimum adet sepetinizde bulunmaktadır.')}}">
            <div class="d-flex w-50 w-md-75">
                <button class="btn btn-secondary w-100 disabled" type="button"><i class="bi bi-basket2 me-2"></i>Sepete Ekle</button>
            </div>
        </div>

    @elseif ($maxQuantity <= 0)

        <div class="d-flex w-100">
            <button class="btn btn-light w-100 disabled" type="button"><i class="bi bi-basket2 me-2"></i>Stokta Yok</button>
        </div>

    @else

        <div class="d-flex gap-2">
            <div class="w-100px rounded border overflow-hidden h-50px position-relative" data-bs-toggle="tooltip" title="Adet">
                <div>
                    <input type="text" name="quantity" class="fw-bold px-3 border-0 w-75px h-50px text-center" value="1" min="1" max="{{$maxQuantity}}" onchange="afterQuantityChange(this)">
                </div>
                <div class="d-flex flex-column w-25px position-absolute top-0 end-0 h-50px justify-content-center">
                    <span class="btn btn-light rounded-0 p-0 border-0 h-25px d-flex align-items-center justify-content-center btn-qty-plus" data-type="plus" data-field="quantity" onclick="quantityChange(this)">
                        <i class="bi bi-plus fs-5"></i>
                    </span>
                    <span class="btn btn-light rounded-0 p-0 border-0 h-25px d-flex align-items-center justify-content-center btn-qty-minus" data-type="minus" data-field="quantity" onclick="quantityChange(this)">
                        <i class="bi bi-dash fs-5"></i>
                    </span>
                </div>
            </div>
            <div>
                <button class="btn btn-primary h-50px py-0 d-flex align-items-center px-3 btn-add-to-cart" type="submit"><i class="bi bi-basket2 me-2"></i>Sepete Ekle</button>
            </div>
        </div>

    @endif


</div>