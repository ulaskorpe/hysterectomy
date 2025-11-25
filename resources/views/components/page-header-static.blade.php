<section @class(['bg-dark'])>

    <div class="container text-white min-h-300px d-flex flex-column justify-content-end pb-5">
        <h1 class="h2 mb-0">{{ $title }}</h1>
        @if ($subTitle)
        <p class="fs-lg mb-0">{{ $subTitle }}</p>
        @endif
    </div>

</section>