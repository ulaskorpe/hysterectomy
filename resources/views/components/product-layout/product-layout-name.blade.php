<div>
    <h1 class="h3 fw-bold mb-0 text-primary">{{$name}}</h1>
    @if ($sku)
    <div class="d-flex align-items-center gap-2 fw-semibold mt-3 pt-2 border-top">
        <i class="bi bi-upc-scan" data-bs-toggle="tooltip" title="Ürün Kodu"></i>
        <span>{{ $sku }}</span>
    </div>
    @endif
</div>