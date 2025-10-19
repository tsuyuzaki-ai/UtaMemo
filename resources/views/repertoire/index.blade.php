<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UtaMemo - カラオケレパートリー管理</title>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    <style>
        body {
            font-family: 'Hiragino Sans', 'ヒラギノ角ゴシック', 'Yu Gothic', 'メイリオ', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
        }

        .header h1 {
            color: #333;
            margin: 0;
            font-size: 2.5em;
        }

        .controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .sort-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #545b62;
        }

        .add-song-btn {
            background-color: #28a745;
            color: white;
            text-decoration: none;
            display: inline-block;
        }

        .add-song-btn:hover {
            background-color: #1e7e34;
        }

        .repertoire-list {
            display: grid;
            gap: 15px;
        }

        .song-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: white;
            transition: box-shadow 0.3s;
        }

        .song-item:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .album-image {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            margin-right: 15px;
            object-fit: cover;
        }

        .song-info {
            flex: 1;
        }

        .song-title {
            font-size: 1.2em;
            font-weight: bold;
            margin: 0 0 5px 0;
            color: #333;
        }

        .song-artist {
            color: #666;
            margin: 0 0 10px 0;
        }

        .song-meta {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .favorite {
            color: #ff6b6b;
            font-size: 1.5em;
            cursor: pointer;
        }

        .skill-level {
            display: flex;
            gap: 2px;
        }

        .star {
            color: #ffd700;
            font-size: 1.2em;
        }

        .star.empty {
            color: #ddd;
        }

        .key-info {
            background: #e9ecef;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.9em;
        }

        .updated-at {
            color: #999;
            font-size: 0.9em;
        }

        .no-songs {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🎤 UtaMemo</h1>
            <p>カラオケレパートリー管理アプリ</p>
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
        <div class="controls">
            <div class="sort-buttons">

            </div>
        </div>

        <div class="repertoire-list" id="repertoireList">
            @if(count($repertoires) > 0)
            @foreach($repertoires as $song)
            <div class="song-item" data-skill="{{ $song['skill_level'] }}" data-favorite="{{ $song['is_favorite'] ? '1' : '0' }}" data-updated="{{ $song['updated_at']->timestamp }}">
                <img src="{{ $song['album_image'] }}" alt="{{ $song['title'] }}" class="album-image">
                <div class="song-info">
                    <h3 class="song-title">{{ $song['title'] }}</h3>
                    <p class="song-artist">{{ $song['artist'] }}</p>
                    <div class="song-meta">
                        <span class="favorite" onclick="toggleFavorite({{ $song['id'] }})">
                            {{ $song['is_favorite'] ? '❤️' : '🤍' }}
                        </span>
                        <div class="skill-level">
                            @for($i = 1; $i <= 3; $i++)
                                <span class="star {{ $i <= $song['skill_level'] ? '' : 'empty' }}">★</span>
                                @endfor
                        </div>
                        <span class="key-info">
                            キー: {{ $song['key'] > 0 ? '+' : '' }}{{ $song['key'] }}
                        </span>
                        <span class="updated-at">
                            {{ $song['updated_at']->format('Y/m/d H:i') }}
                        </span>
                    </div>
                </div>
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

    <script>
        function filterBy(value) {
            const container = document.getElementById('repertoireList');
            const items = Array.from(container.children);

            items.forEach(item => {
                let show = true;

                if (value === 'favorite') {
                    show = item.dataset.favorite === '1';
                } else if (typeof value === 'number') {
                    show = parseInt(item.dataset.skill) === value;
                }
                // 'all' の場合は全て表示なので show = true のまま

                item.style.display = show ? '' : 'none';
            });
        }


        function toggleFavorite(songId) {
            // 後でAPIエンドポイントを実装
            console.log('Toggle favorite for song:', songId);
        }
    </script>
</body>

</html>