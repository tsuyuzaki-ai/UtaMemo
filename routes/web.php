<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RepertoireController;
use App\Http\Controllers\SearchController;

Route::get('/', [RepertoireController::class, 'index'])->name('repertoire.index');
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::post('/search', [SearchController::class, 'search'])->name('search.search');