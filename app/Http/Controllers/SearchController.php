<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// APIを使用するため
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PhpParser\Builder\FunctionLike;



// クラス（設計図）
class SearchController extends Controller
{
    // プロパティ（情報） private→このクラスでのみ使用
    private $spotifyClientId;
    private $spotifyClientSecret;
    private $accessToken;

    // メソッド（機能） __construct→初期発動
    public function __construct()
    {
        // クラスのプロパティに代入 $this→クラス
        $this->spotifyClientId = env('SPOTIFY_CLIENT_ID');
        $this->spotifyClientSecret = env('SPOTIFY_CLIENT_SECRET');
        $this->accessToken = $this->getAccessToken();
    }













    // 一覧表示
    public function index()
    {
        return view('search.index');
    }











    // 検索
    // Request→クラス $request→実態（インスタンス）
    public function search(Request $request)
    {
        // query→「米津」とか
        $query = $request->input('query');

        // 空だったら空を投げ返す　
        if (empty($query)) {
            return response()->json([]);
        }

        try {
            $results = $this->searchSpotify($query);

            // フロントに返す
            return response()->json($results);

            // 例外処理 ログ残す
        } catch (\Exception $e) {
            Log::error('Spotify search error: ' . $e->getMessage());
            return response()->json(['error' => '検索に失敗しました'], 500);
        }
    }







    // Spotify API（初期発動に紐付け）
    private function getAccessToken()
    {
        try {
            // $response→返ってきたもの
            $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
                'grant_type' => 'client_credentials',
                'client_id' => $this->spotifyClientId,
                'client_secret' => $this->spotifyClientSecret,
            ]);
            // 返ってきたものの'access_token'のみ取得
            if ($response->successful()) {
                return $response->json()['access_token'];
            }
            // 例外処理
        } catch (\Exception $e) {
            Log::error('Spotify token error: ' . $e->getMessage());
        }

        // $response->successful() が falseの場合
        return null;
    }

    // 検索に紐付け
    private function searchSpotify($query)
    {
        if (!$this->accessToken) {
            throw new \Exception('Spotify access token not available');
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
        ])->get('https://api.spotify.com/v1/search', [
                    'q' => $query,
                    'type' => 'track',
                    'limit' => 50,
                    'market' => 'JP'
                ]);

        if (!$response->successful()) {
            throw new \Exception('Spotify API request failed');
        }

        $data = $response->json();
        $tracks = $data['tracks']['items'] ?? [];

        $results = [];
        foreach ($tracks as $track) {
            $results[] = [
                'id' => $track['id'],
                'name' => $track['name'],
                'artist' => $track['artists'][0]['name'] ?? 'Unknown Artist',
                'album' => $track['album']['name'] ?? 'Unknown Album',
                'image' => $track['album']['images'][1]['url'] ?? null,
                'preview_url' => $track['preview_url'] ?? null,
            ];
        }

        return $results;
    }
}
