<div class="mb-5 mb-xl-7">
    <h2>Bu Ürünler de İlginizi Çekebilir</h2>
    <div class="swiper related-products-swiper">
        <div class="swiper-wrapper pb-4">
            @foreach ($product->relatedProducts() as $item)
            <div class="swiper-slide">
                <x-cards.product-card :product="$item" />
            </div>
            @endforeach
        </div>
        <div class="swiper-pagination"></div>
    </div>
</div>