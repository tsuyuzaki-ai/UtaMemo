
// トップページ タブ切り替え
function filterBy(value) {
    const container = document.getElementById('repertoireList');
    const items = Array.from(container.children);

    items.forEach(item => {
        let show = true; //全部にtrue付与 全部表示

        if(value === 'favorite'){
            // 比較式の結果をそのまま代入
            show = item.dataset.favorite === '1'; //show = trueまたはfalseになる

        } else if  (typeof value === 'number') { 
            show = parseInt(item.dataset.skill) === value; // valueは 1,2,3いづれか
        }
        // ALLの場合（show=trueそのまま）

        item.style.display = show ? '' : 'none';
    });
}

window.filterBy = filterBy;
// トップページ タブ切り替え

