<x-fe-layout>

    <x-page-header-static :title="__('Ödeme')" />

    <section class="content-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <h2 class="h5">{{__('Üye Girişi')}}</h2>
                    <x-login-form />
                    <hr class="my-4">
                    <a href="{{route('payment.guest')}}" class="btn btn-primary w-100">{{__('Üye olmadan devam et')}}</a>
                </div>
            </div>
        </div>
    </section>

</x-fe-layout>