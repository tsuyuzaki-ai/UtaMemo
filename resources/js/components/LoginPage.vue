<template>
    <div class="auth-container">
        <div class="auth-form">
            <h2>ログイン</h2>
            <form @submit.prevent="login">
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
                    />
                </div>
                
                <button type="submit" class="auth-btn" :disabled="loading">
                    {{ loading ? 'ログイン中...' : 'ログイン' }}
                </button>
            </form>
            
            <div class="auth-links">
                <p>アカウントをお持ちでない方は <router-link to="/register">こちら</router-link></p>
            </div>
        </div>
    </div>
</template>

<script>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { getCsrfToken } from '@/main'

export default {
    name: 'LoginPage',
    setup() {
        const router = useRouter()
        const form = ref({
            email: '',
            password: ''
        })
        const loading = ref(false)

        const login = async () => {
            loading.value = true
            
            try {
                const response = await fetch('/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify(form.value)
                })

                const result = await response.json()

                if (response.status === 403) {
                    router.push('/email/verify')
                    return
                }

                if (result.success) {
                    router.push('/')
                } else if (result.message) {
                    alert(result.message)
                }
            } catch (error) {
                console.error('ログインエラー:', error)
                alert('ログインに失敗しました')
            } finally {
                loading.value = false
            }
        }

        return {
            form,
            loading,
            login
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
    background-color: #1a1a1a;
}

.auth-form {
    background: #2d2d2d;
    color: #ffffff;
    padding: 2rem;
    border-radius: 8px;
    border: 1px solid #333333;
    box-shadow: 0 2px 10px rgba(0,0,0,0.3);
    width: 100%;
    max-width: 400px;
}

.auth-form h2 {
    text-align: center;
    margin-bottom: 1.5rem;
    color: #ffffff;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #cccccc;
    font-weight: 500;
}

.form-input {
    width: 100%;
    padding: 0.75rem;
    background-color: #000000;
    color: #ffffff;
    border: 1px solid #404040;
    border-radius: 4px;
    font-size: 1rem;
    box-sizing: border-box;
}

.form-input::placeholder {
    color: #888888;
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
    font-size: 0.9rem;
}

.auth-links a {
    color: #1db954;
    text-decoration: none;
}

.auth-links a:hover {
    text-decoration: underline;
}
</style>
