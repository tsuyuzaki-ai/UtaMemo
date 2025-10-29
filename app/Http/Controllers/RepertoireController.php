<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Repertoire;

class RepertoireController extends Controller
{
    // レパートリー一覧
    public function index()
    {
        $repertoires = Repertoire::orderBy('is_favorite', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();
        
        return view('repertoire.index', compact('repertoires'));
    }

    // SPA用: レパートリー一覧API
    public function apiIndex()
    {
        // フロントエンドでソートするため、ここでは更新日時順のみでソート
        $repertoires = Repertoire::where('user_id', auth()->id())
            ->orderBy('updated_at', 'desc')
            ->get();
        
        return response()->json($repertoires);
    }

    // SPA用: 曲詳細ページ（welcome.blade.phpを返す）
    public function showForSpa($id)
    {
        $repertoire = Repertoire::find($id);
        
        if (!$repertoire) {
            abort(404, '曲が見つかりません');
        }
        
        return view('welcome', ['pageData' => []]);
    }

    // SPA用: 曲詳細API
    public function apiShow($id)
    {
        $repertoire = Repertoire::where('user_id', auth()->id())->find($id);
        
        if (!$repertoire) {
            return response()->json(['error' => '曲が見つかりません'], 404);
        }
        
        return response()->json($repertoire);
    }

    // 曲詳細・編集ページ
    public function show($id)
    {
        $song = Repertoire::where('user_id', auth()->id())->find($id);

        if (!$song) {
            abort(404, '曲が見つかりません');
        }

        return view('song.show', compact('song'));
    }

    // 曲情報更新
    public function update(Request $request, $id)
    {
        $request->validate([
            'is_favorite' => 'boolean',
            'skill_level' => 'integer|min:0|max:3',
            'key' => 'integer|min:-7|max:7'
        ]);

        $repertoire = Repertoire::where('user_id', auth()->id())->find($id);

        if (!$repertoire) {
            return response()->json([
                'success' => false,
                'message' => '曲が見つかりません'
            ], 404);
        }

        $repertoire->update($request->only(['is_favorite', 'skill_level', 'key']));

        return response()->json([
            'success' => true,
            'message' => '更新しました'
        ]);
    }

    // レパートリーに曲を追加
    public function add(Request $request)
    {
        $request->validate([
            'track_id' => 'required|string',
            'name' => 'required|string|max:255',
            'artist' => 'required|string|max:255',
            'image' => 'nullable|string|max:500'
        ]);

        $repertoire = Repertoire::create([
            'user_id' => auth()->id(),
            'track_id' => $request->track_id,
            'title' => $request->name,
            'artist' => $request->artist,
            'album_image' => $request->image,
            'is_favorite' => false,
            'skill_level' => 0,
            'key' => 0
        ]);

        return response()->json([
            'success' => true,
            'message' => 'レパートリーに追加しました',
            'song_id' => $repertoire->id
        ]);
    }

    // レパートリーから曲を削除
    public function destroy($id)
    {
        $repertoire = Repertoire::where('user_id', auth()->id())->find($id);
        
        if (!$repertoire) {
            return response()->json([
                'success' => false,
                'message' => '曲が見つかりません'
            ], 404);
        }

        $repertoire->delete();
        
        return response()->json([
            'success' => true,
            'message' => '削除しました'
        ]);
    }
}