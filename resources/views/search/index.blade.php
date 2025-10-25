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
    <div class="search-container">
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

    <script>


        let searchTimeout;
        const searchInput = document.getElementById('searchInput');
        const searchResults = document.getElementById('searchResults');
        const loading = document.getElementById('loading');

        searchInput.addEventListener('input', function () {
            // query→検索語
            const query = this.value.trim();

            // 「よ」「ね」のとき「よ」のタイマーリセット
            clearTimeout(searchTimeout);

            if (query.length < 2) {
                searchResults.style.display = 'none';
                // これ以降をスキップ
                return;
            }

            // 500ms後に検索を実行
            searchTimeout = setTimeout(() => {
                searchTracks(query);
            }, 500);
        });


        // 材料イメージ
        // [
        //     [
        //         'name' => 'Chocolate Disco',
        //         'artist' => 'Perfume',
        //         'album_name' => 'GAME',
        //         'image' => 'https://i.scdn.co/image/def456'
        //     ]
        // ]



        async function searchTracks(query) {
            loading.style.display = 'block';
            searchResults.style.display = 'none';

            try {
                const response = await fetch('{{ route('search.search') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                            'content') || ''
                    },
                    body: JSON.stringify({
                        query: query
                    })
                });

                const results = await response.json();
                displayResults(results);
            } catch (error) {
                console.error('検索エラー:', error);
                displayError();
            } finally {
                loading.style.display = 'none';
            }
        }



        function displayResults(results) {
            if (results.error) {
                displayError(results.error);
                return;
            }

            // NGワードを定義（小文字で比較）
            const NG_WORDS = ["映画", "remix", "demo", "live", "cover", "ver", "video", "remaster"];

            // 結果をフィルタリング
            const filteredResults = results.filter(track => {
                const text = (track.name + track.album_name).toLowerCase();
                return !NG_WORDS.some(word => text.includes(word.toLowerCase()));
            });

            if (filteredResults.length === 0) {
                searchResults.innerHTML = '<div class="no-results">検索結果が見つかりませんでした</div>';
                searchResults.style.display = 'block';
                return;
            }

            let html = '';
            filteredResults.forEach(track => {
                html += `
            <div class="result-item">
                <img src="${track.image || '/img/logo01.png'}" alt="アルバム画像" class="album-image">
                <div class="track-info">
                    <div class="track-name">${escapeHtml(track.name)}</div>
                    <div class="artist-name">${escapeHtml(track.artist)}</div>
                </div>
            </div>
        `;
            });

            searchResults.innerHTML = html;
            searchResults.style.display = 'block';
        }


        function displayError(message = '検索中にエラーが発生しました') {
            searchResults.innerHTML = `<div class="no-results">${message}</div>`;
            searchResults.style.display = 'block';
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>

</html>