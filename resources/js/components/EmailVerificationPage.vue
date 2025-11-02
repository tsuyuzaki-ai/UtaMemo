<template>
    <div class="verification-container">
        <div class="verification-form">
            <h2>メール認証</h2>
            <div v-if="!user?.email_verified_at" class="verification-content">
                <p>登録されたメールアドレスに認証メールを送信しました。</p>
                <p>メール内のリンクをクリックして認証を完了してください。</p>
                
                <div class="verification-actions">
                    <button @click="resendEmail" class="resend-btn" :disabled="loading">
                        {{ loading ? '送信中...' : '認証メールを再送信' }}
                    </button>
                </div>
            </div>
            
            <div v-else class="verified-content">
                <p class="success-message">✅ メールアドレスが認証されました！</p>
                <router-link to="/" class="home-link">ホームに戻る</router-link>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { getCsrfToken, apiUrl } from '@/main'

export default {
    name: 'EmailVerificationPage',
    setup() {
        const router = useRouter()
        const user = ref(null)
        const loading = ref(false)

        const fetchUser = async () => {
            try {
                const response = await fetch(apiUrl('/me'), {
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken()
                    }
                })
                
                if (response.ok) {
                    const result = await response.json()
                    user.value = result.user
                }
            } catch (error) {
                console.error('ユーザー情報取得エラー:', error)
            }
        }

        const resendEmail = async () => {
            loading.value = true
            
            try {
                const response = await fetch(apiUrl('/email/resend'), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    }
                })

                const result = await response.json()
                alert(result.message)
                
                if (result.success) {
                    await fetchUser() // ユーザー情報を再取得
                }
            } catch (error) {
                console.error('再送信エラー:', error)
                alert('再送信に失敗しました')
            } finally {
                loading.value = false
            }
        }

        onMounted(() => {
            fetchUser()
        })

        return {
            user,
            loading,
            resendEmail
        }
    }
}
</script>

<style scoped>
.verification-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background-color: #f5f5f5;
}

.verification-form {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 500px;
    text-align: center;
}

.verification-form h2 {
    margin-bottom: 1.5rem;
    color: #333;
}

.verification-content p {
    margin-bottom: 1rem;
    color: #555;
    line-height: 1.6;
}

.verification-actions {
    margin-top: 2rem;
}

.resend-btn {
    background-color: #1db954;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.3s;
}

.resend-btn:hover:not(:disabled) {
    background-color: #1ed760;
}

.resend-btn:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}

.verified-content {
    padding: 1rem 0;
}

.success-message {
    color: #1db954;
    font-size: 1.2rem;
    font-weight: bold;
    margin-bottom: 1.5rem;
}

.home-link {
    display: inline-block;
    background-color: #1db954;
    color: white;
    text-decoration: none;
    padding: 0.75rem 1.5rem;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.home-link:hover {
    background-color: #1ed760;
}
</style>
