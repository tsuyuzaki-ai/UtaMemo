<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RepertoireController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\AuthController;

// SPA用の全ルート（全て同じviewを返す）
Route::get('/', function () {
    return view('welcome', ['pageData' => []]);
});
Route::get('/search', function () {
    return view('welcome', ['pageData' => ['searchUrl' => route('search.search')]]);
});
Route::get('/song/{id}', [RepertoireController::class, 'showForSpa']);

// 認証ルート
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/me', [AuthController::class, 'me']);
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])->middleware('signed')->name('verification.verify');
Route::post('/email/resend', [AuthController::class, 'resend'])->name('verification.resend');

// API ルート（認証・メール認証必須）
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/api/repertoires', [RepertoireController::class, 'apiIndex']);
    Route::get('/api/song/{id}', [RepertoireController::class, 'apiShow']);

    // POST・DELETEルート
    Route::post('/song/{id}/update', [RepertoireController::class, 'update'])->name('song.update');
    Route::post('/repertoire/add', [RepertoireController::class, 'add'])->name('repertoire.add');
    Route::delete('/repertoire/{id}/delete', [RepertoireController::class, 'destroy'])->name('repertoire.destroy');
});

// 検索APIは未ログインでも使用可能
Route::post('/search', [SearchController::class, 'search'])->name('search.search');