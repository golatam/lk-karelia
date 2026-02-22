@extends('layouts.auth')

@section('title'){{ 'У вас нет таких полномочий. Ошибка 403' }}@endsection

@section('content')
    <div class="error">
        <div class="error__text">
            У вас нет таких полномочий. Вернитесь на <a href="{{ route('dashboard') }}" title="Главную страницу">главную страницу</a>
        </div>
        <div class="error__img">

        </div>
    </div>
@endsection
