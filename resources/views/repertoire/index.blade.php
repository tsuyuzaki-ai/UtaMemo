<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UtaMemo - カラオケレパートリー管理</title>
    <link rel="shortcut icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    @vite('resources/css/style.css')
    @vite('resources/js/app.js')
</head>

<body>
    <div id="app"></div>
    
    <script>
        window.pageType = 'repertoire';
        window.repertoires = @json($repertoires);
    </script>
</body>

</html>
