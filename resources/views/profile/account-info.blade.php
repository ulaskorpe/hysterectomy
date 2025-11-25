<x-fe-layout>
    
    <x-page-header-static :title="__('Hesap Bilgilerim')" />

    <div class="content-section">
        <div class="container">

            @include('profile.partials.profile-menu')

            @include('profile.partials.update-profile-form')

        </div>
    </div>

</x-fe-layout>
