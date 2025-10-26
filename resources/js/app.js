import './bootstrap';

// vueのエントリーファイルを作成
import { createApp } from 'vue';
import App from './App.vue'; // 後で作るコンポーネント

// #app要素が存在する場合のみVue.jsをマウント
const appElement = document.getElementById('app');
if (appElement) {
    createApp(App).mount('#app');
}
