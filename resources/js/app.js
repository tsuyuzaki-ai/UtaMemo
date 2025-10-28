import './bootstrap';
import './main'; // 共通ユーティリティ関数

// vueのエントリーファイルを作成
import { createApp } from 'vue';
import App from './App.vue';
import router from './router';

// #app要素が存在する場合のみVue.jsをマウント
const appElement = document.getElementById('app');
if (appElement) {
    const app = createApp(App);
    app.use(router);
    app.mount('#app');
}
