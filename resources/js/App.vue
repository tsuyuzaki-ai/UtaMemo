<template>
  <div>
    <RepertoirePage v-if="pageType === 'repertoire'" :initial-repertoires="repertoires" />
    <SearchPage v-else-if="pageType === 'search'" :search-url="searchUrl" />
    <SongDetailPage v-else-if="pageType === 'song'" :initial-song="song" />
  </div>
</template>

<script>
import RepertoirePage from './components/RepertoirePage.vue'
import SearchPage from './components/SearchPage.vue'
import SongDetailPage from './components/SongDetailPage.vue'

export default {
  name: "App",
  components: {
    RepertoirePage,
    SearchPage,
    SongDetailPage
  },
  data() {
    return {
      pageType: window.pageType || 'welcome',
      repertoires: window.repertoires || [],
      searchUrl: window.searchUrl || '',
      song: window.song || null
    }
  }
};
</script>

<style scoped>
/* styles are in style.css */
</style>
