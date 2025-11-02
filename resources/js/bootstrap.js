import axios from 'axios';
window.axios = axios;

// ベースURLの設定（サブディレクトリ対応）
const baseURL = '/utamemo';
window.axios.defaults.baseURL = baseURL;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// グローバルにbaseURLを公開（fetchで使用するため）
window.APP_BASE_URL = baseURL;
