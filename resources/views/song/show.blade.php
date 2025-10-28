<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $song['title'] }} - UtaMemo</title>
    <link rel="shortcut icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon">
    @vite('resources/css/app.css')
    @vite('resources/css/style.css')
    @vite('resources/js/app.js')
</head>

<body>
    <div id="app"></div>
    
    <script>
        window.pageType = 'song';
        window.song = @json($song);
    </script>
</body>

</html>
