<template>
    <div class="auth-container">
        <div class="auth-form">
            <h2>ユーザー登録</h2>
            <form @submit.prevent="register">
                <div class="form-group">
                    <label for="email">メールアドレス</label>
                    <input
                        type="email"
                        id="email"
                        v-model="form.email"
                        required
                        class="form-input"
                    />
                </div>
                
                <div class="form-group">
                    <label for="password">パスワード</label>
                    <input
                        type="password"
                        id="password"
                        v-model="form.password"
                        required
                        class="form-input"
                        minlength="6"
                    />
                </div>
                
                <div class="form-group">
                    <label for="password_confirmation">パスワード（確認）</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        required
                        class="form-input"
                        minlength="6"
                    />
                </div>
                
                <button type="submit" class="auth-btn" :disabled="loading">
                    {{ loading ? '登録中...' : '登録' }}
                </button>
            </form>
            
            <div class="auth-links">
                <p>すでにアカウントをお持ちの方は <router-link to="/login">こちら</router-link></p>
            </div>
        </div>
    </div>
</template>

<script>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { getCsrfToken } from '@/main'

export default {
    name: 'RegisterPage',
    setup() {
        const router = useRouter()
        const form = ref({
            email: '',
            password: '',
            password_confirmation: ''
        })
        const loading = ref(false)

        const register = async () => {
            if (form.value.password !== form.value.password_confirmation) {
                alert('パスワードが一致しません')
                return
            }

            loading.value = true
            
            try {
                const response = await fetch('/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify(form.value)
                })

                const result = await response.json()

                if (result.success) {
                    router.push('/email/verify')
                    return
                }
            } catch (error) {
                console.error('登録エラー:', error)
                alert('登録に失敗しました')
            } finally {
                loading.value = false
            }
        }

        return {
            form,
            loading,
            register
        }
    }
}
</script>

<style scoped>
.auth-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background-color: #f5f5f5;
}

.auth-form {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 400px;
}

.auth-form h2 {
    text-align: center;
    margin-bottom: 1.5rem;
    color: #333;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #555;
    font-weight: 500;
}

.form-input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
    box-sizing: border-box;
}

.form-input:focus {
    outline: none;
    border-color: #1db954;
    box-shadow: 0 0 0 2px rgba(29, 185, 84, 0.2);
}

.auth-btn {
    width: 100%;
    background-color: #1db954;
    color: white;
    border: none;
    padding: 0.75rem;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.3s;
    margin-bottom: 1rem;
}

.auth-btn:hover:not(:disabled) {
    background-color: #1ed760;
}

.auth-btn:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}

.auth-links {
    text-align: center;
}

.auth-links a {
    color: #1db954;
    text-decoration: none;
}

.auth-links a:hover {
    text-decoration: underline;
}
</style>
