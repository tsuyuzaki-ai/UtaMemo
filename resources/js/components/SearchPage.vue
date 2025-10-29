<template>
    <div class="search-container" :data-search-url="searchUrl">
        <div class="header">
            <h1><img :src="logoUrl" alt="UtaMemo" /></h1>
        </div>
        <router-link to="/" class="back-link">
            <img :src="backImageUrl" alt="戻る" />
        </router-link>

        <div class="search-form">
            <input
                type="text"
                v-model="searchQuery"
                class="search-input"
                placeholder="曲名やアーティスト名を入力..."
                autocomplete="off"
                @input="handleSearchInput"
            />
        </div>

        <div v-if="loading" class="loading">
            検索中...
        </div>

        <div v-if="!loading && searchResults.length > 0" class="search-results">
            <div
                v-for="track in filteredResults"
                :key="track.id"
                class="result-item"
                :data-track-id="track.id"
            >
                <img :src="track.image || '/img/logo01.png'" alt="アルバム画像" class="album-image">
                <div class="track-info">
                    <div class="track-name">{{ track.name }}</div>
                    <div class="artist-name">{{ track.artist }}</div>
                </div>
                
                <button
                    class="add-to-repertoire-btn"
                    :class="{ added: track.added, disabled: track.adding }"
                    :disabled="track.adding || track.added"
                    @click="addToRepertoire(track)"
                >
                    {{ track.adding ? '追加中...' : (track.added ? '追加しました！' : '追加') }}
                </button>
            </div>
        </div>

        <div v-if="!loading && searchQuery.length >= 2 && searchResults.length === 0 && hasSearched" class="no-results">
            検索結果が見つかりませんでした
        </div>
    </div>
</template>

<script>
import { ref, computed } from 'vue'
import { getCsrfToken } from '@/main'

export default {
    name: 'SearchPage',
    props: {
        searchUrl: {
            type: String,
            required: true
        }
    },
    setup(props) {
        const searchQuery = ref('')
        const searchResults = ref([])
        const loading = ref(false)
        const hasSearched = ref(false)
        const logoUrl = `${window.location.origin}/img/logo01.svg`
        const backImageUrl = `${window.location.origin}/img/back.png`
        let searchTimeout = null

        const NG_WORDS = ["映画", "remix", "demo", "live", "cover", "ver", "video", "remaster", "カバー", "オルゴール", "バージョン", "instrumental"]

        const filteredResults = computed(() => {
            return searchResults.value.filter(track => {
                const text = (track.name + track.album_name).toLowerCase()
                return !NG_WORDS.some(word => text.includes(word.toLowerCase()))
            })
        })

        const handleSearchInput = () => {
            clearTimeout(searchTimeout)

            if (searchQuery.value.trim().length < 2) {
                searchResults.value = []
                return
            }

            searchTimeout = setTimeout(() => {
                searchTracks(searchQuery.value.trim())
            }, 500)
        }

        const searchTracks = async (query) => {
            loading.value = true
            hasSearched.value = true
            searchResults.value = []

            try {
                const response = await fetch(props.searchUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify({ query })
                })

                const results = await response.json()
                searchResults.value = results.map(track => ({ ...track, adding: false, added: false }))
            } catch (error) {
                console.error('検索エラー:', error)
                searchResults.value = []
            } finally {
                loading.value = false
            }
        }

        const addToRepertoire = async (track) => {
            if (track.adding || track.added) return

            track.adding = true

            try {
                const response = await fetch('/repertoire/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify({
                        track_id: track.id,
                        name: track.name,
                        artist: track.artist,
                        image: track.image,
                        is_favorite: false,
                        skill_level: 0,
                        key: 0
                    })
                })

                const result = await response.json()
                
                if (!response.ok || !result.success) {
                    throw new Error(result.message || '追加に失敗しました')
                }

                track.added = true
                track.adding = false
            } catch (error) {
                console.error('追加エラー:', error)
                track.adding = false
                alert('レパートリーへの追加に失敗しました')
            }
        }

        return {
            searchQuery,
            searchResults,
            loading,
            hasSearched,
            filteredResults,
            handleSearchInput,
            addToRepertoire,
            logoUrl,
            backImageUrl
        }
    }
}
</script>



