@if ($message = Session::get('success'))
<div @class(['alert alert-success rounded-0 mb-0 w-100']) role="alert">
    <div class="container large-container d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <i class="bi bi-check-circle-fill text-success fs-4"></i>
            <div>{{$message}}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
@endif

@if ($message = Session::get('error'))
<div @class(['alert alert-danger rounded-0 mb-0 w-100']) role="alert">
    <div class="container large-container d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <i class="bi bi-x-circle-fill text-danger fs-4"></i>
            <div>{{$message}}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
@endif

@if ($message = Session::get('info'))
<div @class(['alert alert-info rounded-0 mb-0 w-100']) role="alert">
    <div class="container large-container d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <i class="bi bi-exclamation-triangle-fill text-info fs-4"></i>
            <div>{{$message}}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
@endif

@if ($message = Session::get('itemAddedToCart'))
<div @class(['alert alert-success rounded-0 mb-0 w-100']) role="alert">
    <div class="container large-container d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <i class="bi bi-check-circle-fill text-succes fs-4"></i>
            <div>{{$message}}</div>
            <a href="{{route('cart.index')}}" class="btn btn-success btn-sm ms-auto">Sepete Git</a>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
@endif

@if ($errors->any())
<x-errors :errors="$errors" />
@endif