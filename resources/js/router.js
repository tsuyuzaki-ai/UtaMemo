import { createRouter, createWebHistory } from 'vue-router'
import RepertoirePage from './components/RepertoirePage.vue'
import SearchPage from './components/SearchPage.vue'
import SongDetailPage from './components/SongDetailPage.vue'

const routes = [
    {
        path: '/',
        name: 'repertoire',
        component: RepertoirePage,
        props: true
    },
    {
        path: '/search',
        name: 'search',
        component: SearchPage,
        props: { searchUrl: '/search' }
    },
    {
        path: '/song/:id',
        name: 'song',
        component: SongDetailPage,
        props: true
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router

