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
