<template>
  <div class="app-wrapper">
    <router-view />
    <footer>
      <p>© 2025 UtaMemo. All Rights Reserved.</p>
    </footer>
  </div>
</template>

<script>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'

export default {
  name: "App",
  setup() {
    const router = useRouter()

    onMounted(async () => {
      // 認証状態をチェック
      const currentPath = router.currentRoute.value.path
      
      // 認証不要なページはスキップ
      if (currentPath === '/login' || currentPath === '/register' || currentPath === '/email/verify') {
        return
      }
      
      try {
        const response = await fetch('/me', {
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          }
        })
        
        const result = await response.json()

        // 未ログイン
        if (!response.ok || response.status === 401 || !result.user) {
          // ログインしていない場合、ログイン画面にリダイレクト
          console.log('認証が必要です。ログイン画面にリダイレクトします。')
          router.push('/login')
          return
        }

        // 未認証（メール未確認）の場合、メール認証ページへ
        if (!result.user.email_verified_at) {
          if (currentPath !== '/email/verify') {
            router.push('/email/verify')
          }
          return
        }
      } catch (error) {
        console.error('認証チェックエラー:', error)
        router.push('/login')
      }
    })
  }
};
</script>

<style scoped>
/* styles are in style.css */
</style>
