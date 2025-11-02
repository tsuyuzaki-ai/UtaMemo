import { createRouter, createWebHistory } from 'vue-router'
import RepertoirePage from './components/RepertoirePage.vue'
import SearchPage from './components/SearchPage.vue'
import SongDetailPage from './components/SongDetailPage.vue'
import LoginPage from './components/LoginPage.vue'
import RegisterPage from './components/RegisterPage.vue'
import EmailVerificationPage from './components/EmailVerificationPage.vue'

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
    },
    {
        path: '/login',
        name: 'login',
        component: LoginPage
    },
    {
        path: '/register',
        name: 'register',
        component: RegisterPage
    },
    {
        path: '/email/verify',
        name: 'email-verify',
        component: EmailVerificationPage
    }
]

const router = createRouter({
    history: createWebHistory('/utamemo'),
    routes
})

export default router

