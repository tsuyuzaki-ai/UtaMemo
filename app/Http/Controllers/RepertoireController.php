<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RepertoireController extends Controller
{
    // 初期ダミーデータ
    private function getInitialDummyData()
    {
        return [
            1 => [
                'id' => 1,
                'title' => 'サンプル曲1',
                'artist' => 'サンプルアーティスト1',
                'album_image' => 'https://placehold.jp/500x500.png',
                'is_favorite' => true,
                'skill_level' => 3,
                'key' => 0,
                'updated_at' => now()
            ],
            2 => [
                'id' => 2,
                'title' => 'サンプル曲2',
                'artist' => 'サンプルアーティスト2',
                'album_image' => 'https://placehold.jp/500x500.png',
                'is_favorite' => false,
                'skill_level' => 0,
                'key' => 2,
                'updated_at' => now()->subDays(1)
            ]
        ];
    }

    // セッションからダミーデータを取得（なければ初期データを設定）
    private function getDummyData()
    {
        // セッションにdummy_songsが無ければ作る、あれば返す
        if (!session()->has('dummy_songs')) {
            session(['dummy_songs' => $this->getInitialDummyData()]);
        }
        return session('dummy_songs');
    }

    // ダミーデータを更新
    private function updateDummyData($id, $data)
    {
        $songs = $this->getDummyData();

        if (isset($songs[$id])) {
            // 更新データをマージ
            $songs[$id] = array_merge($songs[$id], $data);
            $songs[$id]['updated_at'] = now();

            // セッションに保存
            session(['dummy_songs' => $songs]);
            return true;
        }

        return false;
    }








    // レパートリー一覧
    public function index()
    {
        $repertoires = array_values($this->getDummyData());
        
        // 並び替え: お気に入り順 → 更新日時順
        usort($repertoires, function($a, $b) {
            // お気に入りがfalse(0)の方を後ろに
            if ($a['is_favorite'] != $b['is_favorite']) {
                return $b['is_favorite'] - $a['is_favorite'];
            }
            // 更新日時が新しい方を前に
            return $b['updated_at']->timestamp - $a['updated_at']->timestamp;
        });
        
        return view('repertoire.index', compact('repertoires'));
    }








    // 曲詳細・編集ページ
    // 曲詳細表示
    public function show($id)
    {
        $dummyData = $this->getDummyData();

        // IDが存在しない場合は404エラー
        if (!isset($dummyData[$id])) {
            // 処理を中止
            abort(404, '曲が見つかりません');
        }

        $song = $dummyData[$id];
        // song/show.blade.phpに渡す
        return view('song.show', compact('song'));
    }

    // 曲情報更新
    public function update(Request $request, $id)
    {
        // パリデーションチェック
        $request->validate([
            'is_favorite' => 'boolean',
            'skill_level' => 'integer|min:0|max:3',
            'key' => 'integer|min:-7|max:7'
        ]);

        // ダミーデータを更新
        // $request→送られてきたもの only→指定したキーだーけ
        $success = $this->updateDummyData($id, $request->only(['is_favorite', 'skill_level', 'key']));

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => '更新しました'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => '曲が見つかりません'
            ], 404);
        }
    }






    // レパートリーに曲を追加
    public function add(Request $request)
    {
        // laravelのバリデーション処理
        $request->validate([
            'track_id' => 'required|string',
            'name' => 'required|string|max:255',
            'artist' => 'required|string|max:255',
            'image' => 'nullable|string|max:500'
        ]);
        // $this→同じクラスのメソッド getDummyDataの戻り値
        $songs = $this->getDummyData();

        // 新しいIDを生成（最大ID + 1）
        // key(値=ID)だけ配列にする
        $maxId = empty($songs) ? 0 : max(array_keys($songs));
        $newId = $maxId + 1;

        // 新しい曲を追加
        $newSong = [
            'id' => $newId,
            'title' => $request->name,
            'artist' => $request->artist,
            'album_image' => $request->image,
            'is_favorite' => false,
            'skill_level' => 0,
            'key' => 0,
            'updated_at' => now()
        ];

        $songs[$newId] = $newSong;
        session(['dummy_songs' => $songs]);

        return response()->json([
            'success' => true,
            'message' => 'レパートリーに追加しました',
            'song_id' => $newId
        ]);
    }

    // レパートリーから曲を削除
    public function destroy($id)
    {
        $songs = $this->getDummyData();
        
        if (isset($songs[$id])) {
            unset($songs[$id]);
            session(['dummy_songs' => $songs]);
            
            return response()->json([
                'success' => true,
                'message' => '削除しました'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => '曲が見つかりません'
        ], 404);
    }
}
