<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RepertoireController;
use App\Http\Controllers\SearchController;

Route::get('/', [RepertoireController::class, 'index'])->name('repertoire.index');


// 曲詳細・編集ページ
Route::get('/song/{id}', [RepertoireController::class, 'show'])->name('song.show');
Route::post('/song/{id}/update', [RepertoireController::class, 'update'])->name('song.update');

// 曲検索・追加ページ
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::post('/search', [SearchController::class, 'search'])->name('search.search');
Route::post('/repertoire/add', [RepertoireController::class, 'add'])->name('repertoire.add');
Route::delete('/repertoire/{id}/delete', [RepertoireController::class, 'destroy'])->name('repertoire.destroy');