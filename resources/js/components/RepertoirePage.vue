<template>
    <div class="container">
        <div class="header"></div>

        <div class="controls">
            <div class="filter-buttons">
                <button class="btn btn-primary" @click="filterBy('all')">ALL</button>
                <button class="btn btn-secondary" @click="filterBy('favorite')">お気に入り</button>
                <button class="btn btn-secondary" @click="filterBy(3)">☆3</button>
                <button class="btn btn-secondary" @click="filterBy(2)">☆2</button>
                <button class="btn btn-secondary" @click="filterBy(1)">☆1</button>
                <button class="btn btn-secondary" @click="filterBy(0)">☆0</button>
            </div>

            <a href="/search" class="btn add-song-btn">+ 曲を追加</a>
        </div>

        <div class="repertoire-list">
            <div v-if="repertoires.length > 0">
                <div
                    v-for="song in repertoires"
                    :key="song.id"
                    class="song-item"
                    :style="{ display: isVisible(song) ? '' : 'none' }"
                >
                    <img :src="song.album_image" :alt="song.title" class="album-image">

                    <div class="song-info" @click="goToSongDetail(song.id)">
                        <h3 class="song-title">{{ song.title }}</h3>
                        <p class="song-artist">{{ song.artist }}</p>

                        <div class="song-meta">
                            <span class="favorite">{{ (song.is_favorite === true || song.is_favorite === 1) ? '❤️' : '🤍' }}</span>
                            <div class="skill-level">
                                <span v-for="i in 3" :key="i" class="star" :class="{ empty: i > song.skill_level }">★</span>
                            </div>

                            <span class="key-info">
                                キー: {{ formatKey(song.key) }}
                            </span>
                            <span class="updated-at">
                                {{ formatDate(song.updated_at) }}
                            </span>
                        </div>
                    </div>
                    
                    <button
                        class="delete-btn"
                        :data-song-id="song.id"
                        @click.stop="deleteSong(song.id)"
                    >
                        削除
                    </button>
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
import { ref } from 'vue'
import { getCsrfToken } from '@/main'

export default {
    name: 'RepertoirePage',
    props: {
        initialRepertoires: {
            type: Array,
            required: true
        }
    },
    setup(props) {
        const repertoires = ref(props.initialRepertoires)
        const currentFilter = ref('all')

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

        const goToSongDetail = (songId) => {
            window.location.href = `/song/${songId}`
        }

        const deleteSong = async (songId) => {
            if (!confirm('この曲を削除しますか？')) {
                return
            }

            try {
                const response = await fetch(`/repertoire/${songId}/delete`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    }
                })

                if (!response.ok) {
                    throw new Error('削除に失敗しました')
                }

                location.reload()
            } catch (error) {
                console.error('削除エラー:', error)
                alert('削除に失敗しました')
            }
        }

        return {
            repertoires,
            currentFilter,
            filterBy,
            isVisible,
            formatKey,
            formatDate,
            goToSongDetail,
            deleteSong
        }
    }
}
</script>



