<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>曲検索 - UtaMemo</title>
    <link rel="shortcut icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon">
    @vite('resources/css/app.css')
    @vite('resources/css/style.css')
    @vite('resources/js/app.js')
</head>

<body>
    <div id="app"></div>
    
    <script>
        window.pageType = 'search';
        window.searchUrl = '{{ route('search.search') }}';
    </script>
</body>

</html>
