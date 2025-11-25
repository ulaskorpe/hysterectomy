<a data-bs-toggle="offcanvas" href="#cart-mini" role="button" @class(['d-flex position-relative text-decoration-none']) id="toggle-mini-cart">
    <i class="bi bi-basket text-light fs-3"></i>
    <div @class([
        'd-flex w-20px h-20px rounded-circle bg-dark text-light position-absolute bottom-0 end-0 align-items-center justify-content-center fs-xs mb-n1 me-n2',
        'opacity-0' => Jackiedo\Cart\Facades\Cart::getDetails()->get('items_count') === 0
    ]) id="cart-items-count">
        {{Jackiedo\Cart\Facades\Cart::getDetails()->get('items_count')}}
    </div>
</a>