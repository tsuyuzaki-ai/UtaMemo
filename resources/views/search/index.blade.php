<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>曲検索 - UtaMemo</title>
    <link rel="shortcut icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    @vite('resources/css/style.css')
    @vite('resources/js/main.js')
</head>

<body>
    <div class="search-container" data-search-url="{{ route('search.search') }}">
        <a href="{{ route('repertoire.index') }}" class="back-link">← 戻る</a>

        <!-- <h1>曲を検索</h1> -->

        <div class="search-form">
            <input type="text" id="searchInput" class="search-input" placeholder="曲名やアーティスト名を入力..." autocomplete="off">
        </div>

        <div id="loading" class="loading" style="display: none;">
            検索中...
        </div>

        <div id="searchResults" class="search-results">
            <!-- 検索結果がここに表示されます -->
        </div>
    </div>

</body>

</html>