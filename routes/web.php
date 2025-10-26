<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RepertoireController;
use App\Http\Controllers\SearchController;

Route::get('/', [RepertoireController::class, 'index'])->name('repertoire.index');
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::post('/search', [SearchController::class, 'search'])->name('search.search');

// 曲詳細・編集ページ
Route::get('/song/{id}', [RepertoireController::class, 'show'])->name('song.show');
Route::post('/song/{id}/update', [RepertoireController::class, 'update'])->name('song.update');

// レパートリー追加
Route::post('/repertoire/add', [RepertoireController::class, 'add'])->name('repertoire.add');