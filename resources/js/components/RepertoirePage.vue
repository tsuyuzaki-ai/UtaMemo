<template>
    <div class="container">
        <div class="header">
            <h1><img :src="logoUrl" alt="UtaMemo" /></h1>
        </div>

        <div v-if="loading" class="loading">読み込み中...</div>

        <div class="controls" v-if="!loading">
            <div class="filter-buttons">
                <button 
                    class="btn filter-btn" 
                    :class="{ active: currentFilter === 'all' }"
                    @click="filterBy('all')"
                >ALL</button>
                <button 
                    class="btn filter-btn" 
                    :class="{ active: currentFilter === 'favorite' }"
                    @click="filterBy('favorite')"
                >お気に入り.</button>
                <button 
                    class="btn filter-btn" 
                    :class="{ active: currentFilter === 3 }"
                    @click="filterBy(3)"
                >☆3</button>
                <button 
                    class="btn filter-btn" 
                    :class="{ active: currentFilter === 2 }"
                    @click="filterBy(2)"
                >☆2</button>
                <button 
                    class="btn filter-btn" 
                    :class="{ active: currentFilter === 1 }"
                    @click="filterBy(1)"
                >☆1</button>
                <button 
                    class="btn filter-btn" 
                    :class="{ active: currentFilter === 0 }"
                    @click="filterBy(0)"
                >☆0</button>
            </div>

            <router-link to="/search" class="btn add-song-btn">+ 曲を追加</router-link>
        </div>

        <div class="repertoire-list">
            <div v-if="repertoires.length > 0">
                <div
                    v-for="song in sortedRepertoires"
                    :key="song.id"
                    class="song-item"
                >
                    <button
                        class="delete-btn"
                        :data-song-id="song.id"
                        @click.stop="deleteSong(song.id)"
                    >
                        ×
                    </button>
                    
                    <img :src="song.album_image" :alt="song.title" class="album-image">

                    <div class="song-info" @click="goToSongDetail(song.id)">
                        <h3 class="song-title">{{ truncateTitle(song.title) }}</h3>
                        <p class="song-artist">{{ song.artist }}</p>

                        <div class="song-meta">
                            <img 
                                :src="getHeartIconUrl(song)" 
                                alt="お気に入り" 
                                class="favorite-icon"
                                :class="{ active: (song.is_favorite === true || song.is_favorite === 1) }"
                            />
                            <div class="skill-level">
                                <img 
                                    v-for="i in 3" 
                                    :key="i" 
                                    :src="getStarIconUrl(i, song.skill_level)"
                                    alt="星"
                                    class="star-icon"
                                    :class="{ active: i <= song.skill_level }"
                                />
                            </div>

                            <span class="key-info">
                                キー: {{ formatKey(song.key) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="no-songs">
                <h3>まだレパートリーがありません</h3>
                <p>「曲を追加」ボタンから曲を追加してみましょう！</p>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { getCsrfToken, apiUrl } from '@/main'

export default {
    name: 'RepertoirePage',
    props: {
        initialRepertoires: {
            type: Array,
            default: () => []
        }
    },
    setup(props) {
        const router = useRouter()
        const repertoires = ref([])
        const currentFilter = ref('all')
        const loading = ref(false)
        const logoUrl = `${window.location.origin}/utamemo/img/logo01.svg`
        const baseImageUrl = `${window.location.origin}/utamemo`

        const getHeartIconUrl = (song) => {
            const isFavorite = song.is_favorite === true || song.is_favorite === 1
            return isFavorite 
                ? `${baseImageUrl}/img/heart_active.svg` 
                : `${baseImageUrl}/img/heart.svg`
        }

        const getStarIconUrl = (index, skillLevel) => {
            const isActive = index <= skillLevel
            return isActive 
                ? `${baseImageUrl}/img/star_active.svg` 
                : `${baseImageUrl}/img/star.svg`
        }

        // データをロード
        const loadRepertoires = async () => {
            loading.value = true
            try {
                const response = await fetch(apiUrl('/api/repertoires'))
                if (response.ok) {
                    repertoires.value = await response.json()
                }
            } catch (error) {
                console.error('データ取得エラー:', error)
            } finally {
                loading.value = false
            }
        }

        onMounted(() => {
            loadRepertoires()
        })

        const filterBy = (value) => {
            currentFilter.value = value
        }

        const isVisible = (song) => {
            if (currentFilter.value === 'all') return true
            if (currentFilter.value === 'favorite') return song.is_favorite === true || song.is_favorite === 1
            if (typeof currentFilter.value === 'number') {
                return song.skill_level === currentFilter.value
            }
            return true
        }

        const sortedRepertoires = computed(() => {
            const filtered = repertoires.value.filter(song => isVisible(song))
            
            // ALLカテゴリーの時は更新日時のみでソート
            if (currentFilter.value === 'all') {
                return filtered.sort((a, b) => {
                    const dateA = new Date(a.updated_at)
                    const dateB = new Date(b.updated_at)
                    return dateB - dateA // 新しい順
                })
            }
            
            // それ以外（お気に入り、星レベル）の時はお気に入りを優先してソート
            return filtered.sort((a, b) => {
                const aFavorite = a.is_favorite === true || a.is_favorite === 1
                const bFavorite = b.is_favorite === true || b.is_favorite === 1
                
                // お気に入りを優先
                if (aFavorite && !bFavorite) return -1
                if (!aFavorite && bFavorite) return 1
                
                // 同じお気に入り状態の場合は更新日時でソート
                const dateA = new Date(a.updated_at)
                const dateB = new Date(b.updated_at)
                return dateB - dateA // 新しい順
            })
        })

        const formatKey = (key) => {
            if (key === 0) return '標準'
            return key > 0 ? `+${key}` : key.toString()
        }

        const truncateTitle = (title) => {
            if (!title) return ''
            
            // スマホサイズかどうかを判定（768px以下）
            const isMobile = window.innerWidth <= 768
            
            if (!isMobile) {
                return title
            }
            
            // 全角10文字（20バイト）を超える場合は省略
            let byteCount = 0
            let result = ''
            
            for (let i = 0; i < title.length; i++) {
                const char = title[i]
                // UTF-8でのバイト数を計算（全角文字は3バイト、半角は1バイト）
                // 簡易的に、Unicodeの範囲で判定（0x00FFより大きい文字は全角扱い）
                const charBytes = char.charCodeAt(0) > 0x00FF ? 2 : 1
                
                if (byteCount + charBytes > 20) {
                    return result + '...'
                }
                
                result += char
                byteCount += charBytes
            }
            
            return result
        }

        const formatDate = (dateString) => {
            const date = new Date(dateString)
            const year = date.getFullYear()
            const month = String(date.getMonth() + 1).padStart(2, '0')
            const day = String(date.getDate()).padStart(2, '0')
            const hours = String(date.getHours()).padStart(2, '0')
            const minutes = String(date.getMinutes()).padStart(2, '0')
            return `${year}/${month}/${day} ${hours}:${minutes}`
        }

        const goToSongDetail = (songId) => {
            router.push(`/song/${songId}`)
        }

        const deleteSong = async (songId) => {
            if (!confirm('この曲を削除しますか？')) {
                return
            }

            try {
                const response = await fetch(apiUrl(`/repertoire/${songId}/delete`), {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    }
                })

                if (!response.ok) {
                    throw new Error('削除に失敗しました')
                }

                // 削除後、配列から削除
                repertoires.value = repertoires.value.filter(song => song.id !== songId)
            } catch (error) {
                console.error('削除エラー:', error)
                alert('削除に失敗しました')
            }
        }

        return {
            repertoires,
            currentFilter,
            loading,
            filterBy,
            isVisible,
            sortedRepertoires,
            formatKey,
            formatDate,
            goToSongDetail,
            deleteSong,
            logoUrl,
            getHeartIconUrl,
            getStarIconUrl,
            truncateTitle
        }
    }
}
</script>



