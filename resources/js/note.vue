<template>
    <div class="controls">
        <div class="finlter-buttons">
            <button class="btn btn-primary" @click="fulterBy('all')">
                ALL
            </button>
            <button class="btn btn-secondary" @click="filterBy('favorite')">
                お気に入り
            </button>
        </div>
    </div>

    <div class="repertoire-list">
        <div v-if="repertoires.length > 0">
            <div
                v-for="song in repertoires"
                :key="song.id"
                class="song-item"
                :style="{ display: isVisible(song) ? '' : 'none' }"
            >
                <img
                    :src="song.album_image"
                    :alt="song.title"
                    class="album-image"
                />

                <div class="song-info" @click="goToSongDetail(song.id)">
                    <h3 class="song-title">{{ song.title }}</h3>
                    <p class="song-artist">{{ song.artist }}</p>

                    <div class="song-meta">
                        <span class="favorite">{{
                            song.is_favorite === true || song.is_favorite === 1
                                ? ""
                                : ""
                        }}</span>
                        <div class="skill-level">
                            <span
                                v-for="i in 3"
                                :key="i"
                                class="star"
                                :class="{ empty: i > song.skill_level }"
                            ></span>
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
                    class="belete-btn"
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
</template>
