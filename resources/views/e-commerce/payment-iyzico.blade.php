<x-fe-layout>

    <x-page-header-static :title="__('Ödeme')" />

    <div class="content-section">
        <div class="container">
            <div class="row g-3 g-xl-5">
                <div class="col-lg-4">
                    <div class="border p-3 rounded">
                        <h2 class="p-responsive">Sepet Özeti</h2>
                        <x-cart-mini :removeButtons="false"></x-cart-mini>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div id="iyzipay-checkout-form" class="responsive"></div>
                </div>
            </div>
        </div>
    </div>

    @push('footer')
        <div id="iyzico-script">{!! $script !!}</div>
    @endpush

</x-fe-layout>