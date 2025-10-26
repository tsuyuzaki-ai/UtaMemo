<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $song['title'] }} - UtaMemo</title>
    <link rel="shortcut icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    @vite('resources/css/style.css')
    @vite('resources/js/main.js')
</head>

<body>
    <div class="song-detail-container" 
         data-song-id="{{ $song['id'] }}"
         data-initial-favorite="{{ $song['is_favorite'] ? 'true' : 'false' }}"
         data-initial-skill-level="{{ $song['skill_level'] }}"
         data-initial-key="{{ $song['key'] }}">
        <a href="{{ route('repertoire.index') }}" class="song-back-link">← 戻る</a>

        <div class="song-detail-header">
            <img src="{{ $song['album_image'] }}" alt="アルバム画像" class="song-album-image-large">
            <div class="song-detail-info">
                <h1 class="song-detail-title">{{ $song['title'] }}</h1>
                <p class="song-detail-artist">{{ $song['artist'] }}</p>
            </div>
        </div>

        <div class="song-edit-section">
            <h2>編集</h2>
            
            <!-- お気に入り -->
            <div class="song-edit-item">
                <label class="song-edit-label">お気に入り</label>
                <div class="song-favorite-container">
                    <span class="song-favorite-icon {{ $song['is_favorite'] ? 'active' : '' }}" 
                          data-action="toggle-favorite">
                        ♥
                    </span>
                </div>
            </div>

            <!-- 上達度 -->
            <div class="song-edit-item">
                <label class="song-edit-label">上達度</label>
                <div class="song-skill-level-container">
                    <div class="song-skill-level">
                        @for($i = 1; $i <= 3; $i++)
                            <span class="song-star {{ $i <= $song['skill_level'] ? 'active' : '' }}" 
                                  data-level="{{ $i }}" 
                                  data-action="set-skill-level">
                                ★
                            </span>
                        @endfor
                    </div>
                </div>
            </div>

            <!-- キー -->
            <div class="song-edit-item">
                <label class="song-edit-label">キー</label>
                <div class="song-key-container">
                    <button class="song-key-btn song-key-down" data-action="adjust-key" data-direction="down">◀︎</button>
                    <span class="song-key-display" id="song-key-display-{{ $song['id'] }}">@if($song['key'] == 0)標準@else{{ $song['key'] > 0 ? '+' : '' }}{{ $song['key'] }}@endif</span>
                    <button class="song-key-btn song-key-up" data-action="adjust-key" data-direction="up">▶︎</button>
                </div>
            </div>
        </div>

        <div class="song-detail-meta">
            <p class="song-updated-at">最終更新: {{ $song['updated_at']->format('Y/m/d H:i') }}</p>
        </div>
    </div>
</body>

</html>
