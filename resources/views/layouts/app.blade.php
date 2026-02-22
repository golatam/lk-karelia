<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    @vite('resources/sass/main.scss')
    @stack('custom-styles')
    <link rel="shortcut icon" href="{{ asset("assets/images/favicon.png") }}" />
    <title>@yield('title')</title>
</head>
<body>
    <div class="page">
        <header class="page__header header">
            @include("include.header")
        </header>
        <div class="page__content">
            <aside class="page__sidebar sidebar">
                @include("partial.sidebar.items")
            </aside>
            <main class="page__main main">
                @stack("header")
                @yield('content')
            </main>
        </div>
        <footer class="page__footer footer">
            @include("include.footer")
        </footer>
    </div>
    <script src="{{ asset("assets/plugins/tinymce/tinymce.min.js") }}"></script>
    @vite('resources/js/main.js')
    <script src="https://initrk.intradesk.ru/chatapp.js" async webChatKey="ba56a563-6e83-400d-a8b7-36693ff6c9b0"></script>
    <script>

      $(document).ready(function (){

      });

    </script>
</body>
</html>
