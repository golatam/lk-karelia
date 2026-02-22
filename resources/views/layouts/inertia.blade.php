<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset("assets/images/favicon.png") }}" />
    <title>{{ config('app.name', 'Личный кабинет ИБ РК') }}</title>

    {{-- Legacy styles (Font Awesome icons, base styles) --}}
    @vite('resources/sass/main.scss')
    {{-- Tailwind styles --}}
    @vite('resources/css/app.css')

    @inertiaHead
</head>
<body>
    @inertia

    {{-- Vue 3 + Inertia app --}}
    @vite('resources/js/app.js')
</body>
</html>
