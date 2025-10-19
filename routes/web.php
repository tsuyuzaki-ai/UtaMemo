<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RepertoireController;

Route::get('/', [RepertoireController::class, 'index'])->name('repertoire.index');


