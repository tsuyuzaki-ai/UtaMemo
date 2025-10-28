<template>
    <div class="song-detail-container" :data-song-id="song.id">
        <a href="/" class="song-back-link">← 戻る</a>

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
                    <span class="song-favorite-icon" :class="{ active: isFavorite }" @click="toggleFavorite">
                        ♥
                    </span>
                </div>
            </div>

            <!-- 上達度 -->
            <div class="song-edit-item">
                <label class="song-edit-label">上達度</label>
                <div class="song-skill-level-container">
                    <div class="song-skill-level">
                        <span
                            v-for="i in 3"
                            :key="i"
                            class="song-star"
                            :class="{ active: i <= skillLevel }"
                            @click="setSkillLevel(i)"
                        >
                            ★
                        </span>
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

        <div class="song-detail-meta">
            <p class="song-updated-at">最終更新: {{ formatDate(song.updated_at) }}</p>
        </div>
    </div>
</template>

<script>
import { ref } from 'vue'
import { getCsrfToken } from '@/main'

export default {
    name: 'SongDetailPage',
    props: {
        initialSong: {
            type: Object,
            required: true
        }
    },
    setup(props) {
        const isFavorite = ref(props.initialSong.is_favorite === true || props.initialSong.is_favorite === 1)
        const skillLevel = ref(props.initialSong.skill_level)
        const currentKey = ref(props.initialSong.key)

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
            const newLevel = skillLevel.value === level ? 0 : level
            skillLevel.value = newLevel
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
                const response = await fetch(`/song/${props.initialSong.id}/update`, {
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
                location.reload()
            }
        }

        return {
            song: props.initialSong,
            isFavorite,
            skillLevel,
            currentKey,
            formatKey,
            formatDate,
            toggleFavorite,
            setSkillLevel,
            adjustKey
        }
    }
}
</script>

<style scoped>
/* styles are in style.css */
</style>

