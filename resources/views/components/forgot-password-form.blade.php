<form method="POST" action="{{ route('password.email') }}" class="needs-validation" novalidate>
    @csrf
        @honeypot
    <div class="form-floating mb-3">
        <input type="email" class="form-control bg-light" name="email" value="{{old('email')}}" autocomplete="username" required id="forgot-email" placeholder="{{__('E-posta')}}"/>
        <label for="forgot-email">{{__('E-Posta')}}</label>
    </div>
    <button class="btn btn-success px-4" type="submit">{{__('Gönder')}}</button>
    @if ($errors->get('email'))
        <div class="alert alert-danger mt-4 mb-0 p-2">
            <ul class="mb-0 list-unstyled">
                @foreach ($errors->all() as $error)
                <li><small>{{ $error }}</small></li>
                @endforeach
            </ul>
        </div>
    @endif
</form>