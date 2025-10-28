<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UtaMemo - カラオケレパートリー管理</title>

    <link rel="shortcut icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    @vite('resources/css/style.css')
    @vite('resources/js/main.js')
</head>

<body>
    <div class="container">
        <div class="header">
            {{-- <h1><img class="logo" width="100" src="{{ asset('img/logo01.svg') }}" alt="サイトロゴ"></h1> --}}
        </div>

        <div class="controls">
            <div class="filter-buttons">
                <button class="btn btn-primary" onclick="filterBy('all')">ALL</button>
                <button class="btn btn-secondary" onclick="filterBy('favorite')">お気に入り</button>
                <button class="btn btn-secondary" onclick="filterBy(3)">☆3</button>
                <button class="btn btn-secondary" onclick="filterBy(2)">☆2</button>
                <button class="btn btn-secondary" onclick="filterBy(1)">☆1</button>
                <button class="btn btn-secondary" onclick="filterBy(0)">☆0</button>
            </div>

            <a href="/search" class="btn add-song-btn">+ 曲を追加</a>
        </div>


        <div class="repertoire-list" id="repertoireList">
            @if(count($repertoires) > 0)
            @foreach($repertoires as $song)

            <div class="song-item" data-skill="{{ $song['skill_level'] }}" data-favorite="{{ $song['is_favorite'] ? '1' : '0' }}" data-updated="{{ $song['updated_at']->timestamp}}">
                <img src="{{ $song['album_image'] }}" alt="{{ $song['title']}}" class="album-image">

                <div class="song-info" onclick="goToSongDetail({{ $song['id'] }})">
                    <h3 class="song-title">{{ $song['title']}}</h3>
                    <p class="song-artist">{{ $song['artist']}}</p>

                    <div class="song-meta">

                        <span class="favorite">{{ $song['is_favorite'] ? '❤️' : '🤍'}}</span>
                        <div class="skill-level">
                            @for($i = 1; $i <= 3; $i++)
                                <span class="star {{ $i <= $song['skill_level'] ? '' : 'empty' }}">★</span>
                            @endfor
                        </div>

                        <span class="key-info">
                             キー: @if($song['key'] == 0)標準@else{{ $song['key'] > 0 ? '+' : '' }}{{ $song['key'] }}@endif
                        </span>
                        <span class="updated-at">
                              {{ $song['updated_at']->format('Y/m/d H:i') }}
                        </span>
                    </div>
                </div>
                
                <button class="delete-btn" data-action="delete-song" data-song-id="{{ $song['id'] }}" onclick="event.stopPropagation(); deleteSong(this);">削除</button>
            </div>
            @endforeach
            @else
            <div class="no-songs">
                <h3>まだレパートリーがありません</h3>
                <p>「曲を追加」ボタンから曲を追加してみましょう！</p>
            </div>
            @endif
        </div>
    </div>

    
</body>

</html>