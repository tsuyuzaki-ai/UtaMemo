<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RepertoireController;
use App\Http\Controllers\SearchController;

// SPA用の全ルート（全て同じviewを返す）
Route::get('/', function () {
    return view('welcome', ['pageData' => []]);
});
Route::get('/search', function () {
    return view('welcome', ['pageData' => ['searchUrl' => route('search.search')]]);
});
Route::get('/song/{id}', [RepertoireController::class, 'showForSpa']);

// API ルート
Route::get('/api/repertoires', [RepertoireController::class, 'apiIndex']);
Route::get('/api/song/{id}', [RepertoireController::class, 'apiShow']);

// POST・DELETEルート（既存のまま）
Route::post('/song/{id}/update', [RepertoireController::class, 'update'])->name('song.update');
Route::post('/search', [SearchController::class, 'search'])->name('search.search');
Route::post('/repertoire/add', [RepertoireController::class, 'add'])->name('repertoire.add');
Route::delete('/repertoire/{id}/delete', [RepertoireController::class, 'destroy'])->name('repertoire.destroy');