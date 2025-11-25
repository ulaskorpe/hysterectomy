@php
    
    $emailId = Str::uuid();
    $passwordId = Str::uuid();

@endphp

<form method="POST" action="{{ route('login') }}" class="needs-validation d-flex flex-column gap-3 shadow rounded-3 p-4 p-xl-5" novalidate>
    @csrf
    @honeypot
    <div>
        <label class="form-label fs-sm fw-semibold" for="login-email-{{$emailId}}">{{__('E-Posta')}}</label>
        <input type="email" class="form-control bg-light" name="email" value="{{old('email')}}" autocomplete="username" required id="login-email-{{$emailId}}" placeholder="{{__('E-posta')}}"/>
    </div>
    <div>
        <label class="form-label fs-sm fw-semibold" for="login-password-{{$passwordId}}">{{__('Şifre')}}</label>
        <input type="password" class="form-control bg-light" name="password" value="" required autocomplete="current-password" id="login-password-{{$passwordId}}" placeholder="{{__('Şifre')}}"/>
    </div>
    <div class="d-flex align-items-center gap-3 justify-content-between">
        <button class="btn btn-success px-4" type="submit">{{__('Giriş')}}</button>
        <a href="{{route('password.request')}}" class="link-primary link-offset-3 link-underline-opacity-25 link-underline-opacity-100-hover">
            <small>{{__('Şifremi Unuttum')}}</small>
        </a>
    </div>
    <div class="pt-3 border-top text-center">
        <a href="{{route('register')}}" class="link-primary link-offset-3 link-underline-opacity-25 link-underline-opacity-100-hover">
            <small>{{__('Yeni Üyelik')}}</small>
        </a>
    </div>
</form>