@extends('layouts.auth')

@section('title'){{ 'Вход в личный кабинет' }}@endsection

@section('content')
<div class="auth-page__box">
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <h1 class="h3 reset-mt">Вход в личный кабинет</h1>
        <div class="form__group form__group--input">
            <input type="email" class="form__input form__input--large @error('email'){{ 'is-invalid' }}@enderror" name="email" value="{{ old('email') }}" required placeholder="Email" autofocus autocomplete="email">
            @error('email')
            <span class="form__message form__message--error" role="alert">{{ $message }}</span>
            @enderror
        </div>
        <div class="form__group form__group--input">
            <input type="password" class="form__input form__input--large @error('password'){{ 'is-invalid' }}@enderror" name="password" required placeholder="Пароль" autocomplete="password">
            @error('password')
            <span class="form__message form__message--error" role="alert">{{ $message }}</span>
            @enderror
        </div>
        <div class="form__group form__group--input">
            <label class="form__checkbox">
                <input class="form__checkbox-input" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <span class="form__checkbox-icon"></span>
                <span class="form__checkbox-label">Запомнить меня</span>
            </label>
        </div>
        <div class="form__group form__group--btn">
            <div class="form__row form__row--ai-center">
                <div class="form__col">
                    <button type="submit" class="btn btn--large btn--green">Войти</button>
                </div>
                <div class="form__col">
                @if (Route::has('password.request'))
                <a class="btn btn--medium btn--white" href="{{ route('password.request') }}">Забыли пароль?</a>
                @endif
                </div>
            </div>
        </div>
        <div class="form__group form__group--btn">
            <div class="form__row form__row--ai-center">
                <div class="form__col">
                    <a class="btn btn--medium btn--white" href="https://docs.init-rk.ru/common/vvedenie" target="_blank">Инструкция по использованию</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
