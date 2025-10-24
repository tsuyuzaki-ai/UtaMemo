<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    private $spotifyClientId;
    private $spotifyClientSecret;
    private $accessToken;

    public function __construct()
    {
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
    public function search(Request $request)
    {
        $query = $request->input('query');

        if (empty($query)) {
            return response()->json([]);
        }

        try {
            $results = $this->searchSpotify($query);
            return response()->json($results);
        } catch (\Exception $e) {
            Log::error('Spotify search error: ' . $e->getMessage());
            return response()->json(['error' => '検索に失敗しました'], 500);
        }
    }




    // Spotify API
    private function getAccessToken()
    {
        try {
            $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
                'grant_type' => 'client_credentials',
                'client_id' => $this->spotifyClientId,
                'client_secret' => $this->spotifyClientSecret,
            ]);

            if ($response->successful()) {
                return $response->json()['access_token'];
            }
        } catch (\Exception $e) {
            Log::error('Spotify token error: ' . $e->getMessage());
        }

        return null;
    }

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
            'limit' => 20,
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
                'image' => $track['album']['images'][0]['url'] ?? null,
                'preview_url' => $track['preview_url'] ?? null,
            ];
        }

        return $results;
    }
}
