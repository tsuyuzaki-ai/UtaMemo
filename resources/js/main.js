
/* --------------------------------------- 
トップページ
--------------------------------------- */
// タブ切り替え
function filterBy(value) {
    const container = document.getElementById('repertoireList');
    const items = Array.from(container.children);

    items.forEach(item => {
        let show = true; //全部にtrue付与 全部表示

        if (value === 'favorite') {
            // 比較式の結果をそのまま代入
            show = item.dataset.favorite === '1'; //show = trueまたはfalseになる

        } else if (typeof value === 'number') {
            show = parseInt(item.dataset.skill) === value; // valueは 1,2,3いづれか
        }
        // ALLの場合（show=trueそのまま）

        item.style.display = show ? '' : 'none';
    });
}

window.filterBy = filterBy;

// 曲詳細ページに遷移
function goToSongDetail(songId) {
    window.location.href = `/song/${songId}`;
}

window.goToSongDetail = goToSongDetail;
/* --------------------------------------- 
トップページ
--------------------------------------- */




/* --------------------------------------- 
検索ページ
--------------------------------------- */

// 入力
let searchTimeout;
const searchInput = document.getElementById('searchInput');
const searchResults = document.getElementById('searchResults');
const loading = document.getElementById('loading');

// 検索ページの要素が存在する場合のみイベントリスナーを設定
if (searchInput) {
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
}





// 参照
async function searchTracks(query) {
    if (!loading || !searchResults) return;
    
    loading.style.display = 'block';
    searchResults.style.display = 'none';

    // 検索URLを取得 .querySelector→IDかクラスから取得
    const searchUrl = document.querySelector('.search-container').dataset.searchUrl;

    // 通常
    try {
        // await→これが終わるまで待つ+fetch（外部から取得）
        const response = await fetch(searchUrl, {
            // 送信する
            method: 'POST',
            headers: {
                // JSONで（トークンと一緒に）
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                    'content') || ''
            },
            // これを
            body: JSON.stringify({
                // JSのオブジェクトの書き方→{ key: value }
                query: query
            })
        });
        // .json→jsonに変換
        const results = await response.json();
        displayResults(results);
        // エラー起きたらここまで飛ぶ
    } catch (error) {
        console.error('検索エラー:', error);
        displayError();
        // どちらにせよここは動く
    } finally {
        loading.style.display = 'none';
    }
}











// 表示
// 引数をresltsと命名する
function displayResults(results) {
    if (!searchResults) return;

    // 材料イメージ
    // results = {
    //     error: "検索中に問題発生", 
    //     data: [
    //         {
    //             name: "曲名",
    //             album_name: "アルバム名",
    //             artist: "アーティスト名",
    //             image: "画像URL"
    //         }
    //     ]
    // }


    if (results.error) {
        displayError(results.error);
        return;
    }

    // NGワードを定義（小文字で比較）
    const NG_WORDS = ["映画", "remix", "demo", "live", "cover", "ver", "video", "remaster", "カバー", "オルゴール", "バージョン", "instrumental"];

    // 結果をフィルタリング
    // track→1曲 任意の名前
    const filteredResults = results.filter(track => {
        // 小文字に統一
        const text = (track.name + track.album_name).toLowerCase();
        return !NG_WORDS.some(word => text.includes(word.toLowerCase()));
    });

    if (filteredResults.length === 0) {
        searchResults.innerHTML = '<div class="no-results">検索結果が見つかりませんでした</div>';
        searchResults.style.display = 'block';
        // 関数displayResultsをここで終える
        return;
    }

    let html = '';
    filteredResults.forEach(track => {
        // バッククオート 変数の埋め込み
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

// エラー
// 引数に初期値を設定
function displayError(message = '検索中にエラーが発生しました') {
    if (!searchResults) return;
    
    searchResults.innerHTML = `<div class="no-results">${message}</div>`;
    searchResults.style.display = 'block';
}

// 記号やコード入れられたらエスケープする
function escapeHtml(text) {
    const div = document.createElement('div');
    // div.textContent = "<script>alert('hi');</script>";
    div.textContent = text;
    // <div>&lt;script&gt;alert('hi');&lt;/script&gt;</div>
    return div.innerHTML;
}











/* --------------------------------------- 
曲詳細ページ
--------------------------------------- */

// お気に入り切り替え
function toggleFavorite(element) {
    const songId = element.closest('.song-detail-container').dataset.songId;
    const isActive = element.classList.contains('active');
    
    // 即座にUIを更新
    element.classList.toggle('active');
    const newFavorite = !isActive;
    
    
    // APIに送信
    updateSong(songId, { is_favorite: newFavorite });
}





// 上達度設定
function setSkillLevel(element) {
    // .dataset→data-をjsで扱えるようにする
    const songId = element.closest('.song-detail-container').dataset.songId;
    const clickedLevel = parseInt(element.dataset.level);
    
    // 現在のスキルレベルを取得（最初に光っている星のレベル）
    const stars = element.parentElement.querySelectorAll('.song-star');
    let currentLevel = 0;
    for (let i = 0; i < stars.length; i++) {
        if (stars[i].classList.contains('active')) {
            currentLevel = i + 1;
        } else {
            break;
        }
    }
    
    // クリックした星のレベルが現在のレベルと同じ場合は0にする、違う場合はそのレベルを設定
    const newLevel = clickedLevel === currentLevel ? 0 : clickedLevel;
    
    // 星の表示を更新
    stars.forEach((star, index) => {
        star.classList.toggle('active', index < newLevel);
    });
    
    // APIに送信
    updateSong(songId, { skill_level: newLevel });
}





// キー調整
function adjustKey(direction, element) {
    const songId = element.closest('.song-detail-container').dataset.songId;
    const keyDisplay = document.getElementById(`song-key-display-${songId}`);
    const currentKey = parseInt(keyDisplay.textContent);
    
    let newKey = currentKey;
    if (direction === 'up' && currentKey < 6) {
        newKey = currentKey + 1;
    } else if (direction === 'down' && currentKey > -6) {
        newKey = currentKey - 1;
    }
    
    if (newKey !== currentKey) {
        keyDisplay.textContent = newKey;
        
        // APIに送信
        updateSong(songId, { key: newKey });
    }
}

// 曲情報更新API
async function updateSong(songId, data) {
    try {
        const response = await fetch(`/song/${songId}/update`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify(data)
        });
        
        if (!response.ok) {
            throw new Error('更新に失敗しました');
        }
        
        const result = await response.json();
        console.log('更新成功:', result.message);
        
    } catch (error) {
        console.error('更新エラー:', error);
        alert('更新に失敗しました');
        
        // エラー時は元の状態に戻す
        location.reload();
    }
}

// グローバル関数として定義
window.toggleFavorite = toggleFavorite;
window.setSkillLevel = setSkillLevel;
window.adjustKey = adjustKey;

// DOM読み込み完了後にイベントリスナーを設定
document.addEventListener('DOMContentLoaded', function() {
    
    // お気に入りハートのクリックイベント
    document.addEventListener('click', function(e) {
        if (e.target.dataset.action === 'toggle-favorite') {
            toggleFavorite(e.target);
        }
    });
    
    // 星のクリックイベント
    document.addEventListener('click', function(e) {
        if (e.target.dataset.action === 'set-skill-level') {
            setSkillLevel(e.target);
        }
    });
    
    // キーボタンのクリックイベント
    document.addEventListener('click', function(e) {
        if (e.target.dataset.action === 'adjust-key') {
            const direction = e.target.dataset.direction;
            adjustKey(direction, e.target);
        }
    });
});

/* --------------------------------------- 
検索ページ
--------------------------------------- */

