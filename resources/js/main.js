// function filterBy(value) {
//     const container = document.getElementById('repertoireList');
//     const items = Array.from(container.children);

//     items.forEach(item => {
//         let show = true;

//         if (value === 'favorite') {
//             show = item.dataset.favorite === '1';
//         } else if (typeof value === 'number') {
//             show = parseInt(item.dataset.skill) === value;
//         }
//         // 'all' の場合は全て表示なので show = true のまま

//         item.style.display = show ? '' : 'none';
//     });
// }

// window.filterBy = filterBy;


function filterBy(value) {
    const container = document.getElementById('repertoireList');
    const items = Array.from(container.children);

    items.forEach(item => {
        let show = true; //全部にtrue付与

        if(value === 'favorite'){
            show = item.dataset.favorite === '1'; //お気に入り表示に上書き
        } else if  (typeof value === 'number') {
            show = parseInt(item.dataset.skill) === value;
        }
    });
}

