@extends('layouts.auth')

@section('title'){{ 'Изменить пароль' }}@endsection

@section('content')
<div class="auth-page__box">
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <h1 class="h3 reset-mt">Изменить пароль</h1>
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div class="form__group form__group--input">
            <input type="email" class="form__input form__input--large @error('email') is-invalid @enderror" name="email" value="{{ old('email', $request->email) }}" placeholder="Email" required autocomplete="email" autofocus>
            @error('email')
            <span class="form__message form__message--error" role="alert">{{ $message }}</span>
            @enderror
        </div>
        <div class="form__group form__group--input">
            <input type="password" class="form__input form__input--large @error('password') is-invalid @enderror" name="password" placeholder="Пароль" required autocomplete="new-password">
            @error('password')
            <span class="form__message form__message--error" role="alert">{{ $message }}</span>
            @enderror
        </div>
        <div class="form__group form__group--input">
            <input type="password" class="form__input form__input--large" placeholder="Повторите пароль" name="password_confirmation" required autocomplete="new-password">
        </div>
        <div class="form__group form__group--btn">
            <button type="submit" class="btn btn--large btn--green">Изменить пароль</button>
        </div>
    </form>
</div>
@endsection
