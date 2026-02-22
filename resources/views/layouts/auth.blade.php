<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <link href="{{ asset('assets/css/app.css?ver='.hash_file('md5','assets/css/app.css')) }}" rel="stylesheet" type="text/css" />
    @stack('custom-styles')
    <link rel="shortcut icon" href="{{ asset("assets/images/favicon.png") }}" />
    <title>@yield('title')</title>
</head>
<body>
    <div class="auth-page">
        @yield('content')
    </div>
    <script src="{{ asset("assets/js/app.js?ver=" . hash_file('md5', 'assets/js/app.js')) }}"></script>
    <script src="https://initrk.intradesk.ru/chatapp.js" async webChatKey="ba56a563-6e83-400d-a8b7-36693ff6c9b0"></script>
</body>
</html>
