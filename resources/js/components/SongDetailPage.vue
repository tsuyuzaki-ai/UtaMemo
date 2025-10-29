<template>
    <div class="song-detail-container" v-if="song" :data-song-id="song.id">
        <div class="header">
            <h1><img :src="logoUrl" alt="UtaMemo" /></h1>
        </div>
        <router-link to="/" class="song-back-link">
            <img :src="backImageUrl" alt="戻る" />
        </router-link>

        <div v-if="loading">読み込み中...</div>

        <div class="song-detail-header">
            <img :src="song.album_image" alt="アルバム画像" class="song-album-image-large">
            <div class="song-detail-info">
                <h1 class="song-detail-title">{{ song.title }}</h1>
                <p class="song-detail-artist">{{ song.artist }}</p>
            </div>
        </div>

        <div class="song-edit-section">
            <h2>編集</h2>
            
            <!-- お気に入り -->
            <div class="song-edit-item">
                <label class="song-edit-label">お気に入り</label>
                <div class="song-favorite-container">
                    <img 
                        :src="heartIconUrl" 
                        alt="お気に入り" 
                        class="song-favorite-icon"
                        :class="{ active: isFavorite }"
                        @click="toggleFavorite"
                    />
                </div>
            </div>

            <!-- 上達度 -->
            <div class="song-edit-item">
                <label class="song-edit-label">上達度</label>
                <div class="song-skill-level-container">
                    <div class="song-skill-level">
                        <img
                            v-for="i in 3"
                            :key="i"
                            :src="i <= skillLevel ? baseImageUrl + '/img/star_active.svg' : baseImageUrl + '/img/star.svg'"
                            alt="星"
                            class="song-star"
                            :class="{ active: i <= skillLevel }"
                            @click.stop="setSkillLevel(i)"
                        />
                    </div>
                </div>
            </div>

            <!-- キー -->
            <div class="song-edit-item">
                <label class="song-edit-label">キー</label>
                <div class="song-key-container">
                    <button class="song-key-btn song-key-down" @click="adjustKey(-1)">◀︎</button>
                    <span class="song-key-display">{{ formatKey(currentKey) }}</span>
                    <button class="song-key-btn song-key-up" @click="adjustKey(1)">▶︎</button>
                </div>
            </div>
        </div>

        <router-link to="/" class="song-back-link song-back-link-bottom">
            <img :src="backImageUrl" alt="戻る" />
        </router-link>

        <div class="song-detail-meta">
            <p class="song-updated-at">最終更新: {{ formatDate(song.updated_at) }}</p>
        </div>
    </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { getCsrfToken } from '@/main'

export default {
    name: 'SongDetailPage',
    setup() {
        const route = useRoute()
        const song = ref(null)
        const loading = ref(false)
        const isFavorite = ref(false)
        const skillLevel = ref(0)
        const currentKey = ref(0)
        const logoUrl = `${window.location.origin}/img/logo01.svg`
        const backImageUrl = `${window.location.origin}/img/back.png`
        const baseImageUrl = window.location.origin

        const heartIconUrl = computed(() => {
            return isFavorite.value 
                ? `${baseImageUrl}/img/heart_active.svg` 
                : `${baseImageUrl}/img/heart.svg`
        })

        // データをロード
        const loadSong = async () => {
            loading.value = true
            try {
                const response = await fetch(`/api/song/${route.params.id}`)
                if (response.ok) {
                    const data = await response.json()
                    song.value = data
                    isFavorite.value = data.is_favorite === true || data.is_favorite === 1
                    skillLevel.value = data.skill_level
                    currentKey.value = data.key
                }
            } catch (error) {
                console.error('データ取得エラー:', error)
            } finally {
                loading.value = false
            }
        }

        onMounted(() => {
            loadSong()
        })

        const formatKey = (key) => {
            if (key === 0) return '標準'
            return key > 0 ? `+${key}` : key.toString()
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

        const toggleFavorite = () => {
            isFavorite.value = !isFavorite.value
            updateSong({ is_favorite: isFavorite.value })
        }

        const setSkillLevel = (level) => {
            console.log('setSkillLevel called with level:', level)
            console.log('current skillLevel.value:', skillLevel.value)
            const newLevel = skillLevel.value === level ? 0 : level
            console.log('newLevel:', newLevel)
            skillLevel.value = newLevel
            console.log('skillLevel.value after update:', skillLevel.value)
            updateSong({ skill_level: newLevel })
        }

        const adjustKey = (direction) => {
            const newKey = currentKey.value + direction
            if (newKey >= -7 && newKey <= 7) {
                currentKey.value = newKey
                updateSong({ key: newKey })
            }
        }

        const updateSong = async (data) => {
            try {
                const response = await fetch(`/song/${song.value.id}/update`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify(data)
                })

                if (!response.ok) {
                    throw new Error('更新に失敗しました')
                }

                const result = await response.json()
                console.log('更新成功:', result.message)
            } catch (error) {
                console.error('更新エラー:', error)
                alert('更新に失敗しました')
            }
        }

        return {
            song,
            loading,
            isFavorite,
            skillLevel,
            currentKey,
            formatKey,
            formatDate,
            toggleFavorite,
            setSkillLevel,
            adjustKey,
            logoUrl,
            backImageUrl,
            heartIconUrl,
            baseImageUrl
        }
    }
}
</script>



