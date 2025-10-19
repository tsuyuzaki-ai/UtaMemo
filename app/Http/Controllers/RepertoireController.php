<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RepertoireController extends Controller
{
    // レパートリー一覧
    public function index()
    {
        // ダミーデータ
        $repertoires = [
            [
                'id' => 1,
                'title' => 'サンプル曲1',
                'artist' => 'サンプルアーティスト1',
                'album_image' => 'https://placehold.jp/500x500.png',
                'is_favorite' => true,
                'skill_level' => 3,
                'key' => 0,
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'title' => 'サンプル曲2',
                'artist' => 'サンプルアーティスト2',
                'album_image' => 'https://placehold.jp/500x500.png',
                'is_favorite' => false,
                'skill_level' => 1,
                'key' => 2,
                'updated_at' => now()->subDays(1)
            ]
        ];

        return view('repertoire.index', compact('repertoires'));
    }
}
