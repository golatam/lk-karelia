@extends('layouts.auth')

@section('title'){{ 'Страница не найдена. Ошибка 404' }}@endsection

@section('content')
    <div class="error">
        <div class="error__text">
            Страница не найдена. Вернитесь на <a href="{{ route('dashboard') }}" title="Главную страницу">главную страницу</a>
        </div>
        <div class="error__img">

        </div>
    </div>
@endsection
