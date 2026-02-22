@extends('layouts.auth')

@section('title'){{ 'Подтверждение пароля' }}@endsection

@section('content')
<div class="auth-page__box">
    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf
        <h1 class="h3 reset-mt">Подтвердить пароль</h1>
        <div class="form__group form__group--input">
            <input type="password" class="form__input form__input--large @error('password') is-invalid @enderror" name="password" placeholder="Пароль" required autocomplete="current-password">
            @error('password')
            <span class="invalid-feedback" role="alert">{{ $message }}</span>
            @enderror
        </div>
        <div class="form__group form__group--btn">
            <div class="form__row form__row--ai-center">
                <div class="form__col">
                    <button type="submit" class="btn btn--large btn--green">Подтвердить</button>
                </div>
                <div class="form__col">
                    @if (Route::has('password.request'))
                        <a class="btn btn--medium btn--white" href="{{ route('password.request') }}">Забыли пароль?</a>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
