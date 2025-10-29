<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Verified;
use App\Models\User;

class AuthController extends Controller
{
    // ログイン画面表示
    public function showLogin()
    {
        return view('welcome', ['pageData' => ['authType' => 'login']]);
    }

    // 登録画面表示
    public function showRegister()
    {
        return view('welcome', ['pageData' => ['authType' => 'register']]);
    }

    // ログイン処理
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // メール未認証ならログアウトして案内
            if (! $user->hasVerifiedEmail()) {
                Auth::logout();
                return response()->json([
                    'success' => false,
                    'message' => 'メール認証が完了していません。メール内のリンクを開いてください。',
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'ログインしました',
                'user' => $user
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'メールアドレスまたはパスワードが正しくありません'
        ], 401);
    }

    // 登録処理
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed'
        ]);

        // ユーザーIDを自動生成（メールアドレスの@より前の部分を使用）
        $emailPrefix = explode('@', $request->email)[0];
        $user_id = $emailPrefix . '_' . time(); // 重複回避のためタイムスタンプ追加

        $user = User::create([
            'name' => $user_id, // ユーザーIDを名前として使用
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_id' => $user_id,
        ]);

        // メール認証メールを送信（ログインさせない）
        $user->sendEmailVerificationNotification();

        return response()->json([
            'success' => true,
            'message' => '登録が完了しました。メール認証をお願いします。',
            'user' => $user
        ]);
    }

    // ログアウト処理
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'ログアウトしました'
        ]);
    }

    // 現在のユーザー情報取得
    public function me()
    {
        return response()->json([
            'user' => Auth::user()
        ]);
    }

    // メール認証処理
    public function verify(Request $request)
    {
        $user = User::findOrFail($request->route('id'));

        // 署名の検証（routesで signed ミドルウェア適用済）
        if (! $request->hasValidSignature()) {
            return redirect('/login')->with('error', 'リンクの有効期限が切れています。再度メールを送信してください。');
        }

        // メールハッシュの検証
        if (! hash_equals((string) $request->route('hash'), sha1($user->email))) {
            return redirect('/login')->with('error', 'リンクが無効です。再度メールを送信してください。');
        }

        if ($user->hasVerifiedEmail()) {
            // 既に認証済みなら自動ログインしてホームへ
            Auth::login($user);
            return redirect('/')->with('already_verified', true);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            
            // 認証後、自動でログインしてホームへリダイレクト
            Auth::login($user);
        }

        // HTMLレスポンスでホームへリダイレクト
        return redirect('/')->with('verified', true);
    }

    // メール再送信
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'メールアドレスは既に認証済みです'
            ]);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'success' => true,
            'message' => '認証メールを再送信しました'
        ]);
    }
}