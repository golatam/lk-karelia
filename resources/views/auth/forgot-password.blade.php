@extends('layouts.auth')

@section('title'){{ 'Сбросить пароль' }}@endsection

@section('content')
<div class="auth-page__box">
@if (session('status'))
    <h1 class="h3 reset-mt">Пароль сброшен</h1>
    <div class="alert alert--success" role="alert">
        {{ session('status') }}
    </div>
@else
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <h1 class="h3 reset-mt">Сбросить пароль</h1>
        <div class="form__group form__group--input">
            <input type="email" class="form__input form__input--large @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Email" required autocomplete="email" autofocus>
            @error('email')
            <span class="form__message form__message--error" role="alert">{{ $message }}</span>
            @enderror
        </div>
        <div class="form__group form__group--btn">
            <button type="submit" class="btn btn--large btn--green">Отправить пароль</button>
        </div>
    </form>
@endif
</div>
@endsection
