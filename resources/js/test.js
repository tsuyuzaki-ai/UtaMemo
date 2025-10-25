let searchTimeout;
const searchInput = document.getElementById('searchInput');
const searchResults = document.getElementById('searchResults');
const loading = document.getElementById('loading');

searchInput.addEventListener('input', function () {
    // query→検索語
    const query = this.value.trim();

    clearTimeout(searchTimeout);

    if (query.length < 2) {
        searchResults.style.display = 'none';
        return;
    }

    searchTimeout = setTimeout(() => {
        searchTracks(query);
    }, 500);
});