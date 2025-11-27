<?php

use App\Http\Controllers\Pages\AboutController;
use App\Http\Controllers\Pages\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about-us', [AboutController::class, 'index'])->name('about');
