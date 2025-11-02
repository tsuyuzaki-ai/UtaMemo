// 全ページ共通のユーティリティ関数

// CSRFトークンを取得する関数
export function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

// HTMLエスケープ関数
export function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// API呼び出し用の共通ヘッダー
export function getApiHeaders() {
    return {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken()
    };
}

// ベースURLを含むURLを生成する関数
export function apiUrl(path) {
    const baseURL = window.APP_BASE_URL || '/utamemo';
    // pathが既にbaseURLを含んでいる場合はそのまま返す
    if (path.startsWith(baseURL)) {
        return path;
    }
    // 先頭のスラッシュを除去して結合
    const cleanPath = path.startsWith('/') ? path.slice(1) : path;
    return `${baseURL}/${cleanPath}`;
}
